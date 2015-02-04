<?php


namespace php\http;

/**
 * Represents a cookie.
 *
 * @package php.http
 */
class CookieInterface {

    protected $name;
    protected $value;
    protected $domain;
    protected $expire;
    protected $path;
    protected $secure;
    protected $httpOnly;

    /**
     * Constructor.
     *
     * @param string               $name     The name of the cookie
     * @param string               $value    The value of the cookie
     * @param int|string|\DateTime $expire   The time the cookie expires
     * @param string               $path     The path on the server in which the cookie will be available on
     * @param string               $domain   The domain that the cookie is available to
     * @param bool                 $secure   Whether the cookie should only be transmitted over a secure HTTPS connection from the client
     * @param bool                 $httpOnly Whether the cookie will be made accessible only through the HTTP protocol
     *
     * @throws \InvalidArgumentException
     *
     * @version 0.0.1-dev
     */
    public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true) {
        // from PHP source code
        if (preg_match("/[=,; \t\r\n\013\014]/", $name) || !preg_match("/[,;\s\W]*/", $name) || preg_match(("/Comment|Discard|Domain|Expires|Max-Age|Path|Secure|Version/i"), $name)) {
            throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('The cookie name cannot be empty.');
        }

        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTime) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);

            if (false === $expire || -1 === $expire) {
                throw new \InvalidArgumentException('The cookie expiration time is not valid.');
            }
        }

        $this->name = $name;
        $this->value = $value;
        $this->domain = $domain;
        $this->expire = $expire;
        $this->path = empty($path) ? '/' : $path;
        $this->secure = (bool) $secure;
        $this->httpOnly = (bool) $httpOnly;
    }

    /**
     * Gets the name of the cookie.
     *
     * @return string
     * @version 0.0.1-dev
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the value of the cookie.
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Gets the domain that the cookie is available to.
     *
     * @return string
     *
     * @version 0.0.1-dev
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Gets the time the cookie expires.
     *
     * @return int
     *
     * @version 0.0.1-dev
     */
    public function getExpiresTime() {
        return $this->expire;
    }

    /**
     * Gets the path on the server in which the cookie will be available on.
     *
     * @return string
     *
     * @version 0.0.1-dev
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Checks whether the cookie should only be transmitted over a secure HTTPS connection from the client.
     *
     * @return bool
     *
     * @version 0.0.1-dev
     */
    public function isSecure() {
        return $this->secure;
    }

    /**
     * Checks whether the cookie will be made accessible only through the HTTP protocol.
     *
     * @return bool
     *
     * @version 0.0.1-dev
     */
    public function isHttpOnly() {
        return $this->httpOnly;
    }

    /**
     * Whether this cookie is about to be cleared.
     *
     * @return bool
     *
     * @version 0.0.1-dev
     */
    public function isCleared() {
        return $this->expire < time();
    }
}