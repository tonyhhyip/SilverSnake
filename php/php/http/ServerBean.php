<?php

namespace php\http;


class ServerBean extends ParameterBean {
    /**
     * Gets the HTTP headers.
     *
     * @return array
     * @version 0.1-dev
     */
    public function getHeaders() {
        $headers = array();
        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
    }
}