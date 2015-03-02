<?php

namespace SilverSnake\Http\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface FilterChain
 * @package SilverSnake\Http\Filter
 */
interface FilterChain {
    /**
     * Causes the next filter in the chain to be invoked, or if the calling filter is the last filter in the chain,
     * causes the resource at the end of the chain to be invoked.
     * @param Request $request
     * @param Response $response
     */
    public function doFilter(Request $request, Response $response);
}