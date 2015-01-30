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
     * @api
     */
    public function init(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) {
        $this->request = new ParameterBaan($request);
        $this->query = new ParameterBean($query);
        $this->attributes = new ParameterBean($attributes);
        $this->cookies = new ParameterBean($cookies);
        $this->files = new FileBean($files);
        $this->server = new ServerBean($server);
        $this->headers = new HeaderBean($this->server->getHeaders);

    }

    /**
     * {inherit}
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * {inherit}
     */
    public function getBasePath() {

    }
}