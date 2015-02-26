<?php

namespace php\http;


class HeaderBean implements \IteratorAggregate, \Countable {

    protected $headers = array(), $cacheControl = array();

    /**
     * Constructor.
     *
     * @param array $headers An array of HTTP headers
     *
     * @version 0.0.1-dev
     */
    public function __construct(array $headers = array()) {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Convert HeaderBean to string
     *
     * @return string
     * @version 0.0.1-dev
     */
    public function __toString() {
        if (!$this->headers) {
            return '';
        }

        $max = max(array_map(function ($x) {
            return strlen($x);
        }, array_keys($this->header))) + 1;
        $content = '';
        ksort($this->headers);
        foreach ($this->headers as $name => $values) {
            $name = implode('-', array_map(function ($x) {
                return ucfirst($x);
            }, explode('-', $name)));
            foreach ($values as $value) {
                $content .= sprintf("%-{$max}s %s: %s\r\n", $name, $value);
            }
        }

        return $content;
    }

    /**
     * Returns the headers.
     *
     * @return array An array of headers
     *
     * @version 0.0.1-dev
     */
    public function getAllHeaders() {
        return $this->headers;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     * @version 0.0.1-dev
     */
    public function getHeaderNames() {
        return array_keys($this->headers);
    }

    /**
     * Replaces the current header with the new set.
     *
     * @param array $headers array of HTTP headers.
     * @version 0.0.1-dev
     */
    public function replace(array $headers) {
        $this->headers = array();
        $this->addHeaders($headers);
    }

    /**
     * Add new headers to current headers.
     *
     * @param array $headers array of HTTP headers/
     * @version 0.0.1-dev
     */
    public function addHeaders(array $headers) {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * Get the header value by name.
     *
     * @param string $key Name of header.
     * @param bool   $first   Whether to return the first value or all header values
     * @return string|array The first header value if $first is true, an array of values otherwise
     * @version 0.0.1-dev
     */
    public function getHeader($key, $first = true) {
        $key = self::transferKey($key);
        if (!array_key_exists($key, $this->headers)) {
            return $first ? null : array();
        }

        return $first ? (count($this->headers[$key]) ? $this->headers[$key][0] : null) : $this->headers[$key];
    }

    /**
     * Sets a header by name.
     *
     * @param string       $key     The key
     * @param string|array $values  The value or an array of values
     * @param bool         $replace Whether to replace the actual value or not (true by default)
     * @version 0.0.1-dev
     */
    public function setHeader($key, $value, $replace = true) {
        $key = self::transferKey($key);

        $value = array_values((array) $value);

        if ($replace || !array_key_exists($key, $this->headers)) {
            $this->headers[$key] = $value;
        } else {
            $this->headers[$key] += $value;
        }

        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl($value[0]);
        }
    }

    /**
     * Returns true if the HTTP header is defined.
     *
     * @param string $key The HTTP header.
     * @return bool true if the parameter exists, false otherwise.
     * @version 0.0.1-dev
     */
    public function hasHeader($key) {
        return array_key_exists(self::transferKey($key), $this->headers);
    }

    /**
     * Removes a header.
     *
     * @param string $key The HTTP header name
     * @version 0.0.1-dev
     */
    public function removeHeader($key) {
        $key = self::transferKey($key);

        unset($this->headers[$key]);

        if ('cache-control' === $key) {
            $this->cacheControl = array();
        }
    }

    /**
     * Adds a custom Cache-Control directive.
     *
     * @param string $key   The Cache-Control directive name
     * @param mixed  $value The Cache-Control directive value
     */
    public function addCacheControlDirective($key, $value = true) {
        $this->cacheControl[$key] = $value;

        $this->setHeader('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns true if the Cache-Control directive is defined.
     *
     * @param string $key The Cache-Control directive
     * @return bool true if the directive exists, false otherwise
     * @version 0.0.1-dev
     */
    public function hasCacheControlDirective($key) {
        return array_key_exists($key, $this->cacheControl);
    }

    /**
     * Returns a Cache-Control directive value by name.
     *
     * @param string $key The directive name
     * @return mixed|null The directive value if defined, null otherwise
     * @version 0.0.1-dev
     */
    public function getCacheControlDirective($key) {
        return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }

    /**
     * Removes a Cache-Control directive.
     *
     * @param string $key The Cache-Control directive
     * @version 0.0.1-dev
     */
    public function removeCacheControlDirective($key) {
        unset($this->cacheControl[$key]);

        $this->setHeader('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * @return string
     * @version 0.0.1-dev
     */
    protected function getCacheControlHeader() {
        $parts = array();
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"'.$value.'"';
                }

                $parts[] = "$key=$value";
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Parses a Cache-Control HTTP header.
     *
     * @param string $header The value of the Cache-Control HTTP header
     *
     * @return array An array representing the attribute values
     * @version 0.0.1-dev
     */
    protected function parseCacheControl($header) {
        $cacheControl = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $cacheControl[strtolower($match[1])] = isset($match[3]) ? $match[3] : (isset($match[2]) ? $match[2] : true);
        }

        return $cacheControl;
    }

    /**
     * Returns an iterator for headers.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     * @version 0.0.1-dev
     */
    public function getIterator() {
        return new \ArrayIterator($this->headers);
    }

    /**
     * Returns the number of headers.
     *
     * @return int The number of headers
     * @version 0.0.1-dev
     */
    public function count() {
        return count($this->headers);
    }

    /**
     * Transfer header key.
     *
     * @param string $key.
     * @return string HTTP Header.
     * @version 0.0.1-dev
     */
    private static function transferKey($key) {
        return strtr(strtolower($key), '_', '-');
    }
}