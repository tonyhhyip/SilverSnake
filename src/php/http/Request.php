<?php

namespace php\http;

/**
 * Request represents an HTTP request.
 *
 * The methods dealing with URL accept / return a raw path (% encoded):
 *   * getBasePath
 *   * getBaseUrl
 *   * getPathInfo
 *   * getRequestUri
 *   * getUri
 *
 * @api
 */
class Request implements RequestInterface{

    const HEADER_CLIENT_IP = 'client_ip';
    const HEADER_CLIENT_HOST = 'client_host';
    const HEADER_CLIENT_PROTO = 'client_proto';
    const HEADER_CLIENT_PORT = 'client_port';

    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PURGE = 'PURGE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    protected static $trustedProxies = array();

    /**
     * @var string[]
     */
    protected static $trustedHostPatterns = array();

    /**
     * @var string[]
     */
    protected static $trustedHosts = array();

    /**
     * Names for headers that can be trusted when
     * using trusted proxies.
     *
     * The default names are non-standard, but widely used
     * by popular reverse proxies (like Apache mod_proxy or Amazon EC2).
     */
    protected static $trustedHeaders = array(
        self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR',
        self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST',
        self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO',
        self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT',
    );

    protected static $httpMethodParameterOverride = false;

    /**
     * @var array
     */
    protected static $formats;

    protected static $requestFactory;


    /**
     * @var \php\http\SessionInterface
     */
    protected $session;

    /**
     * Query string parameters ($_GET).
     *
     * @var \php\http\ParameterBean
     *
     * @api
     */
    protected $query;

    /**
     * Custom parameters.
     *
     * @var \php\http\ParameterBean
     *
     * @api
     */
    protected $attributes;

    /**
     * Request body parameters ($_POST).
     *
     * @var \php\http\ParameterBean
     *
     * @api
     */
    protected $request;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     * @var \php\http\ServerBean
     *
     * @api
     */
    protected $server;

    /**
     * Uploaded files ($_FILES).
     *
     * @var \php\http\FileBean
     *
     * @api
     */
    public $files;

    /**
     * Cookies ($_COOKIE).
     *
     * @var \php\http\ParameterBean
     *
     * @api
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var \php\http\HeaderBean
     *
     * @api
     */
    public $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $languages;

    /**
     * @var array
     */
    protected $charsets;

    /**
     * @var array
     */
    protected $encodings;

    /**
     * @var array
     */
    protected $acceptableContentTypes;

    /**
     * @var string
     */
    protected $pathInfo;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $defaultLocale = 'en';

    /**
     * Creates a new request with values from PHP's super globals.
     *
     * @return Request A new request
     *
     * @version 0.0.1-dev
     */
    public static function createFromGlobals() {
        $server = $_SERVER;
        if ('cli-server' === php_sapi_name()) {
            if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
                $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
            }
            if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
                $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
            }
        }

        $request = self::createRequestFromFactory($_GET, $_POST, array(), $_COOKIE, $_FILES, $server);

        if (0 === strpos($request->headers->getHeader('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->getParameter('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBean($data);
        }

        return $request;
    }

    /**
     * Create Request by factory
     *
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return array|mixed|static
     */
    private static function createRequestFromFactory(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) {
        if (self::$requestFactory) {
            $request = call_user_func(self::$requestFactory, $query, $request, $attributes, $cookies, $files, $server, $content);

            if (!$request instanceof Request) {
                throw new \LogicException('The Request factory must return an instance of \\php\\http\\Request.');
            }

            return $request;
        }

        return new static($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Normalizes a query string.
     *
     * It builds a normalized query string, where keys/value pairs are alphabetized,
     * have consistent escaping and unneeded delimiters are removed.
     *
     * @param string $qs Query string
     *
     * @return string A normalized query string for the Request
     */
    public static function normalizeQueryString($qs) {
        if ('' == $qs) {
            return '';
        }

        $parts = array();
        $order = array();

        foreach (explode('&', $qs) as $param) {
            if ('' === $param || '=' === $param[0]) {
                // Ignore useless delimiters, e.g. "x=y&".
                // Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
                // PHP also does not include them when building _GET.
                continue;
            }

            $keyValuePair = explode('=', $param, 2);

            // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
            // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
            // RFC 3986 with rawurlencode.
            $parts[] = isset($keyValuePair[1]) ?
                rawurlencode(urldecode($keyValuePair[0])).'='.rawurlencode(urldecode($keyValuePair[1])) :
                rawurlencode(urldecode($keyValuePair[0]));
            $order[] = urldecode($keyValuePair[0]);
        }

        array_multisort($order, SORT_ASC, $parts);

        return implode('&', $parts);
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array  $query      The GET parameters
     * @param array  $request    The POST parameters
     * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array  $cookies    The COOKIE parameters
     * @param array  $files      The FILES parameters
     * @param array  $server     The SERVER parameters
     * @param string $content    The raw body data
     *
     * @version 0.0.1-dev
     */
    public function init(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) {
        $this->request = new ParameterBean($request);
        $this->query = new ParameterBean($query);
        $this->attributes = new ParameterBean($attributes);
        $this->cookies = new ParameterBean($cookies);
        $this->files = new FileBean($files);
        $this->server = new ServerBean($server);
        $this->headers = new HeaderBean($this->server->getHeaders());

    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public final function getBasePath() {
        if (null === $this->basePath)
            $this->basePath = $this->prepareBasePath();

        return $this->basePath;
    }

    /**
     * Prepares the base path.
     *
     * @return string base path
     * @version 0.0.1-dev
     */
    protected function prepareBasePath() {
        $filename = basename($this->server->getParameter('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl))
            return '';

        if (basename($baseUrl) === $filename)
            return dirname($baseUrl);
        else
            $basePath = $baseUrl;

        if ('\\' === DIRECTORY_SEPARATOR)
            $basePath = str_replace('\\', '/', $basePath);

        return rtrim($basePath, '/');
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public final function getBaseUrl() {
        if (null === $this->baseUrl)
            $this->baseUrl = $this->prepareBaseUrl();

        return $this->baseUrl;
    }

    /**
     * Prepares the base URL.
     *
     * @return string
     * @version 0.0.1-dev
     */
    protected function prepareBaseUrl() {
        $filename = basename($this->server->getParameter('SCRIPT_FILENAME'));

        if (basename($this->server->getParameter('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->getParameter('SCRIPT_NAME');
        } elseif (basename($this->server->getParameter('PHP_SELF')) === $filename) {
            $baseUrl = $this->server->getParameter('PHP_SELF');
        } elseif (basename($this->server->getParameter('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->getParameter('ORIG_SCRIPT_NAME');
        }else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $this->server->getParameter('PHP_SELF', '');
            $file = $this->server->getParameter('SCRIPT_FILENAME', '');
            $segs = array_reverse(explode('/', trim($file, '/')));
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/$seg$baseUrl';
                $index++;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);

            unset($pos, $index, $last, $path, $file, $segs, $seg);
        }

        // Does the baseUrl have anything in common with the request_uri?
        $requestUri = $this->getRequestUri();

        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }

        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, dirname($baseUrl))) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/');
        }

        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            // no match whatsoever; set it blank
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && $pos !== 0) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getScheme() {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getPort() {
        if (self::$trustedProxies) {
            if (self::$trustedHeaders[self::HEADER_CLIENT_PORT] && $port = $this->headers->getHeader(self::$trustedHeaders[self::HEADER_CLIENT_PORT])) {
                return $port;
            }

            if (self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && 'https' === $this->headers->getHeader(self::$trustedHeaders[self::HEADER_CLIENT_PROTO], 'http')) {
                return 443;
            }
        }

        if ($host = $this->headers->getHeader('HOST')) {
            if ($host[0] === '[') {
                $pos = strpos($host, ':', strrpos($host, ']'));
            } else {
                $pos = strrpos($host, ':');
            }

            if (false !== $pos) {
                return intval(substr($host, $pos + 1));
            }

            return 'https' === $this->getScheme() ? 443 : 80;
        }

        return $this->server->getParameter('SERVER_PORT');
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getHttpHost() {
        $scheme = $this->getScheme();
        $port = $this->getPort();

        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }

        return $this->getHost().':'.$port;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getRequestUri() {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    /*
     * The following methods are derived from code of the Zend Framework (1.10dev - 2010-01-24)
     *
     * Code subject to the new BSD license (http://framework.zend.com/license/new-bsd).
     *
     * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     */

    protected function prepareRequestUri() {
        $requestUri = '';

        if ($this->headers->hasHeader('X_ORIGINAL_URL')) {
            // IIS with Microsoft Rewrite Module
            $requestUri = $this->headers->getHeader('X_ORIGINAL_URL');
            $this->headers->removeHeader('X_ORIGINAL_URL');
            $this->server->remove('HTTP_X_ORIGINAL_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->headers->hasHeader('X_REWRITE_URL')) {
            // IIS with ISAPI_Rewrite
            $requestUri = $this->headers->getHeader('X_REWRITE_URL');
            $this->headers->removeHeader('X_REWRITE_URL');
        } elseif ($this->server->getParameter('IIS_WasUrlRewritten') == '1' && $this->server->getParameter('UNENCODED_URL') != '') {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server->getParameter('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->hasParameter('REQUEST_URI')) {
            $requestUri = $this->server->getParameter('REQUEST_URI');
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->hasParameter('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server->getParameter('ORIG_PATH_INFO');
            if ('' != $this->server->getParameter('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->getParameter('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }

        // normalize the request URI to ease creating sub-requests from this request
        $this->server->setParameter('REQUEST_URI', $requestUri);

        return $requestUri;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getUri() {
        if (null !== $qs = $this->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getQueryString() {
        $qs = static::normalizeQueryString($this->server->getParameter('QUERY_STRING'));

        return '' === $qs ? null : $qs;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function isSecure() {
        if (self::$trustedProxies && self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && $proto = $this->headers->getHeader(self::$trustedHeaders[self::HEADER_CLIENT_PROTO])) {
            return in_array(strtolower(current(explode(',', $proto))), array('https', 'on', 'ssl', '1'));
        }

        $https = $this->server->getParameter('HTTPS');

        return !empty($https) && 'off' !== strtolower($https);
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getHost() {
        if (self::$trustedProxies && self::$trustedHeaders[self::HEADER_CLIENT_HOST] && $host = $this->headers->getHeader(self::$trustedHeaders[self::HEADER_CLIENT_HOST])) {
            $elements = explode(',', $host);

            $host = $elements[count($elements) - 1];
        } elseif (!$host = $this->headers->getHeader('HOST')) {
            if (!$host = $this->server->getParameter('SERVER_NAME')) {
                $host = $this->server->getParameter('SERVER_ADDR', '');
            }
        }

        // trim and remove port number from host
        // host is lowercase as per RFC 952/2181
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        // as the host can come from the user (HTTP_HOST and depending on the configuration, SERVER_NAME too can come from the user)
        // check that it does not contain forbidden characters (see RFC 952 and RFC 2181)
        // use preg_replace() instead of preg_match() to prevent DoS attacks with long host names
        if ($host && '' !== preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host)) {
            throw new \UnexpectedValueException(sprintf('Invalid Host "%s"', $host));
        }

        if (count(self::$trustedHostPatterns) > 0) {
            // to avoid host header injection attacks, you should provide a list of trusted host patterns

            if (in_array($host, self::$trustedHosts)) {
                return $host;
            }

            foreach (self::$trustedHostPatterns as $pattern) {
                if (preg_match($pattern, $host)) {
                    self::$trustedHosts[] = $host;

                    return $host;
                }
            }

            throw new \UnexpectedValueException(sprintf('Untrusted Host "%s"', $host));
        }

        return $host;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getMethod() {
        if (null === $this->method) {
            $this->method = strtoupper($this->server->getParameter('REQUEST_METHOD', 'GET'));

            if ('POST' === $this->method) {
                if ($method = $this->headers->getHeader('X-HTTP-METHOD-OVERRIDE')) {
                    $this->method = strtoupper($method);
                } elseif (self::$httpMethodParameterOverride) {
                    $this->method = strtoupper($this->request->getParameter('_method', $this->query->getParameter('_method', 'POST')));
                }
            }
        }

        return $this->method;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getLocale() {
        return null === $this->locale ? $this->defaultLocale : $this->locale;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getDefaultLocale() {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public final function setLocale($locale) {
        $this->setPhpDefaultLocale($this->locale = $locale);
    }

    /**
     * Sets the default PHP locale.
     *
     * @param string $locale
     */
    private function setPhpDefaultLocale($locale) {
        // if either the class Locale doesn't exist, or an exception is thrown when
        // setting the default locale, the intl module is not installed, and
        // the call can be ignored:
        try {
            if (class_exists('Locale', false)) {
                \Locale::setDefault($locale);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getPreferredLanguage(array $locales = null) {
        $preferredLanguages = $this->getLanguages();

        if (empty($locales)) {
            return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
        }

        if (!$preferredLanguages) {
            return $locales[0];
        }

        $extendedPreferredLanguages = array();
        foreach ($preferredLanguages as $language) {
            $extendedPreferredLanguages[] = $language;
            if (false !== $position = strpos($language, '_')) {
                $superLanguage = substr($language, 0, $position);
                if (!in_array($superLanguage, $preferredLanguages)) {
                    $extendedPreferredLanguages[] = $superLanguage;
                }
            }
        }

        $preferredLanguages = array_values(array_intersect($extendedPreferredLanguages, $locales));

        return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0];
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getLanguages() {
        if (null !== $this->languages) {
            return $this->languages;
        }

        $languages = AcceptHeader::fromString($this->headers->getHeader('Accept-Language'))->all();
        $this->languages = array();
        foreach (array_keys($languages) as $lang) {
            if (strstr($lang, '-')) {
                $codes = explode('-', $lang);
                if ($codes[0] == 'i') {
                    // Language not listed in ISO 639 that are not variants
                    // of any listed language, which can be registered with the
                    // i-prefix, such as i-cherokee
                    if (count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    for ($i = 0, $max = count($codes); $i < $max; $i++) {
                        if ($i == 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_' . strtoupper($codes[$i]);
                        }
                    }
                }
            }

            $this->languages[] = $lang;
        }

        return $this->languages;
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getAcceptableContentTypes() {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }

        return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->headers->getHeader('Accept'))->all());
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public function getEncodings() {
        if (null !== $this->encodings) {
            return $this->encodings;
        }

        return $this->encodings = array_keys(AcceptHeader::fromString($this->headers->getHeader('Accept-Encoding'))->all());
    }

    /**
     * {@inheritdoc}
     *
     * @version 0.0.1-dev
     */
    public final function getPathInfo() {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    /**
     * Prepares the path info.
     *
     * @return string path info
     */
    protected function preparePathInfo() {
        $baseUrl = $this->getBaseUrl();

        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }

        $pathInfo = '/';

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if (null !== $baseUrl && false === $pathInfo = substr($requestUri, strlen($baseUrl))) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }

        return (string) $pathInfo;
    }

    /*
     * Returns the prefix as encoded in the string when the string starts with
     * the given prefix, false otherwise.
     *
     * @param string $string The urlencoded string
     * @param string $prefix The prefix not encoded
     *
     * @return string|false The prefix as it is encoded in $string, or false
     */
    private function getUrlencodedPrefix($string, $prefix) {
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }

        $len = strlen($prefix);
        $match = array();

        if (preg_match(sprintf("#^(%[[:xdigit:]]{2}|.){{%d}}#", $len), $string, $match)) {
            return $match[0];
        }

        return false;
    }

    /**
     * Gets the scheme and HTTP host.
     *
     * If the URL was called with basic authentication, the user
     * and the password are not added to the generated string.
     *
     * @return string The scheme and HTTP host
     */
    public function getSchemeAndHttpHost() {
        return $this->getScheme().'://'.$this->getHttpHost();
    }

}