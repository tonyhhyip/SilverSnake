<?php

namespace php\http;


class ServerBean extends ParameterBean {

    /**
     * Headers.
     * @var array
     */
    private $headers = array();

    /**
     * Gets the HTTP headers.
     *
     * @return array
     * @version 0.1-dev
     */
    public function getHeaders() {
        if (count($this->headers))
            return $this->headers;
        $headers = array();
        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($key, 'HTTP_'))
                $headers[preg_replace('/^HTTP_/', '', $key)] = $value;
            elseif (array_key_exists($key, $contentHeaders))
                $headers[$key] = $value;
        }

        if (array_key_exists('PHP_AUTH_USER', $this->parameters)) {
            $headers['PHP_AUTH_USER'] =  $this->parameters['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = array_key_exists('PHP_AUTH_PW', $this->parameters) ? $this->parameters['PHP_AUTH_PW'] : '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */

            $authHeader = null;
            if (array_key_exists('HTTP_AUTHORIZATION', $this->parameters)) {
                $authHeader = $this->parameters['HTTP_AUTHORIZATION'];
            } elseif (array_key_exists('REDIRECT_HTTP_AUTHORIZATION', $this->parameters)) {
                $authHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authHeader) {
                if (0 === stripos($authHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authHeader, 6)), 2);
                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }
                    unset($exploded);
                } elseif (empty($this->parameters['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $this->parameters['PHP_AUTH_DIGEST'] = $authorizationHeader;
                }
            }
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        unset($authHeader, $contentHeaders);

        $this->headers = $headers;

        return $headers;
    }
}