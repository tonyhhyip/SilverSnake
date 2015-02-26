<?php

namespace php\http;

interface ResponseInterface {
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    /**
     * Sends HTTP headers.
     *
     * @version 0.0.1-dev
     */
    public function sendHeaders();


    /**
     * Sets the response status code.
     *
     * @param int   $code HTTP status code
     * @param mixed $text HTTP status text
     *
     * If the status text is null it will be automatically populated for the known
     * status codes and left empty otherwise.
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     *
     * @version 0.0.1-dev
     */
    public function setStatus($code, $text = null);

    /**
     * Retrieves the status code for the current web response.
     *
     * @return int $status Status code
     * @version 0.0.1-dev
     */
    public function getStatus();

    /**
     * Sends an error response to the client using the specified status code and clears the buffer.
     * The server will preserve cookies and may clear or update any headers needed to serve the error page as a valid response.
     * If an error-page declaration has been made for the web application corresponding to the status code passed in,
     * it will be served back the error page.
     *
     * If the response has already been committed, this method throws an IllegalStateException.
     * After using this method, the response should be considered to be committed and should not be written to.
     *
     * @param int $status Error status code.
     */
    public function sendError($status);

    /**
     * Sends a temporary redirect response to the client using the specified redirect location URL and clears the buffer.
     *
     * @param string $location the redirect location URL.
     * @version 0.0.1-dev
     */
    public function sendRedirect($location);

    /**
     * Gets the value of the response header with the given name.
     * If a response header with the given name exists, contains multiple values and $first is true,
     * the value that was added first will be returned.
     *
     * @param string $name the name of the response header whose value to return.
     * @param bool $first return the first value or not.
     * @return mixed the value of the response header with the given name,
     *          or null if no header with the given name has been set on this response
     * @version 0.0.1-dev
     */
    public function getHeader($name, $first = false);

    /**
     * Get the headers in an array.
     *
     * @return array the headers.
     * @version 0.0.1-dev
     */
    public function getHeaders();

    /**
     * Sets a response header with the given name and value.
     * If the header had already been set, the new value overwrites the previous one.
     *
     * @param string $name name of header.
     * @param string $value the header value.
     * @version 0.0.1-dev
     */
    public function setHeader($name, $value);

    /**
     * Adds a response header with the given name and value.
     * This method allows response headers to have multiple values.
     *
     * @param string $name name of header.
     * @param string $value the header value.
     * @version 0.0.1-dev
     */
    public function addHeader($name, $value);

    /**
     * Sets the response charset.
     *
     * @param string $charset Character set.
     *
     * @version 0.0.1-dev
     */
    public function setCharset($charset);

    /**
     * Retrieves the response charset.
     *
     * @return string Character set
     * @version 0.0.1-dev
     */
    public function getCharset();

    /**
     * Returns the content type used for the MIME body sent in this response.
     * If no content type has been specified, this method returns null.
     *
     * @return string Content type.
     * @version 0.0.1-dev
     */
    public function getContentType();

    /**
     * Sets the content type of the response being sent to the client, if the response has not been committed yet.
     *
     * @param string $type Content-type
     * @version 0.0.1-dev
     */
    public function setContentType($type);

    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     *
     * @param string $version The HTTP protocol version.
     * @version 0.0.1-dev
     */
    public function setProtocolVersion($version);

    /**
     * Gets the HTTP protocol version.
     *
     * @return string The HTTP protocol version
     * @version 0.0.1-dev
     */
    public function getProtocolVersion();
}