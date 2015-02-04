<?php

namespace php\http;

/**
 * Interface RequestInterface for all the request.
 * @package php\http
 */
interface RequestInterface {

    /**
     * Get the parameter of the request.
     *
     * @param string $name name of parameter.
     * @return mixed value of parameter
     */
    public function getParameter($name);

    /**
     * Gets the Session.
     *
     * @return SessionInterface|null The session
     *
     * @version 0.0.1-dev
     */
    public function getSession();

    /**
     * Returns the root path from which this request is executed.
     *
     * Suppose that an index.php file instantiates this request object:
     *
     *  * http://localhost/index.php         returns an empty string
     *  * http://localhost/index.php/page    returns an empty string
     *  * http://localhost/web/index.php     returns '/web'
     *  * http://localhost/we%20b/index.php  returns '/we%20b'
     *
     * @return string The raw path (i.e. not urldecoded)
     *
     * @version 0.0.1-dev
     */
    public function getBasePath();

    /**
     * Returns the root URL from which this request is executed.
     *
     * The base URL never ends with a /.
     *
     * This is similar to getBasePath(), except that it also includes the
     * script filename (e.g. index.php) if one exists.
     *
     * @return string The raw URL (i.e. not urldecoded)
     *
     * @version 0.0.1-dev
     */
    public function getBaseUrl();

    /**
     * Gets the request's scheme.
     *
     * @return string request scheme like HTTP or HTTPS.
     *
     * @version 0.0.1-dev
     */
    public function getScheme();

    /**
     * Returns the port on which the request is made.
     *
     * This method can read the client port from the "X-Forwarded-Port" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Port" header must contain the client port.
     *
     * If your reverse proxy uses a different header name than "X-Forwarded-Port",
     * configure it via "setTrustedHeaderName()" with the "client-port" key.
     *
     * @return string
     *
     * @version 0.0.1-dev
     */
    public function getPort();

    /**
     * Returns the HTTP host being requested.
     *
     * The port name will be appended to the host if it's non-standard.
     *
     * @return string
     *
     * @version 0.0.1-dev
     */
    public function getHttpHost();

    /**
     * Returns the requested URI (path and query string).
     *
     * @return string The raw URI (i.e. not URI decoded)
     *
     * @version 0.0.1-dev
     */
    public function getRequestUri();

    /**
     * Generates a normalized URI (URL) for the Request.
     *
     * @return string A normalized URI (URL) for the Request
     *
     * @see #getQueryString()
     *
     * @version 0.0.1-dev
     */
    public function getUri();

    /**
     * Generates the normalized query string for the Request.
     *
     * It builds a normalized query string, where keys/value pairs are alphabetized
     * and have consistent escaping.
     *
     * @return string|null A normalized query string for the Request
     *
     * @version 0.0.1-dev
     */
    public function getQueryString();

    /**
     * Checks whether the request is secure or not.
     *
     * This method can read the client port from the "X-Forwarded-Proto" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
     *
     * If your reverse proxy uses a different header name than "X-Forwarded-Proto"
     * ("SSL_HTTPS" for instance), configure it via "setTrustedHeaderName()" with
     * the "client-proto" key.
     *
     * @return bool
     *
     * @version 0.0.1-dev
     */
    public function isSecure();

    /**
     * Returns the host name.
     *
     * This method can read the client port from the "X-Forwarded-Host" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Host" header must contain the client host name.
     *
     * If your reverse proxy uses a different header name than "X-Forwarded-Host",
     * configure it via "setTrustedHeaderName()" with the "client-host" key.
     *
     * @return string
     *
     * @throws \UnexpectedValueException when the host name is invalid
     *
     * @version 0.0.1-dev
     */
    public function getHost();

    /**
     * Gets the request "intended" method.
     *
     * If the X-HTTP-Method-Override header is set, and if the method is a POST,
     * then it is used to determine the "real" intended HTTP method.
     *
     * The _method request parameter can also be used to determine the HTTP method,
     * but only if enableHttpMethodParameterOverride() has been called.
     *
     * The method is always an uppercased string.
     *
     * @return string The request method
     *
     * @version 0.0.1-dev
     *
     * @see #getRealMethod()
     */
    public function getMethod();

    /**
     * Get the locale.
     *
     * @return string
     * @version 0.0.1-dev
     */
    public function getLocale();

    /**
     * Get the default locale.
     *
     * @return string
     * @version 0.0.1-dev
     */
    public function getDefaultLocale();

    /**
     * Sets the locale.
     *
     * @param string $locale
     *
     * @version 0.0.1-dev
     */
    public function setLocale($locale);

    /**
     * Returns the preferred language.
     *
     * @param array $locales An array of ordered available locales
     *
     * @return string|null The preferred locale
     *
     * @version 0.0.1-dev
     */
    public function getPreferredLanguage(array $locales = null);


    /**
     * Gets a list of languages acceptable by the client browser.
     *
     * @return array Languages ordered in the user browser preferences
     *
     * @version 0.0.1-dev
     */
    public function getLanguages();

    /**
     * Gets a list of content types acceptable by the client browser.
     *
     * @return array List of content types in preferable order
     *
     * @version 0.0.1-dev
     */
    public function getAcceptableContentTypes();

    /**
     * Gets a list of encodings acceptable by the client browser.
     *
     * @return array List of encodings in preferable order
     * @version 0.0.1-dev
     */
    public function getEncodings();

    /**
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     *
     * @version 0.0.1-dev
     */
    public function getPathInfo();
}