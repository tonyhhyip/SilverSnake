<?php

namespace SilverSnake\Filter;

use SilverSnake\Controller\ControllerContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Interface Filter
 * @package SilverSnake\Http
 */
interface Filter {
    /**
     * Called by the web container to indicate to a filter that it is being placed into service.
     * The init method must complete successfully before the filter is asked to do any filtering work.
     *
     * The web container cannot place the filter into service if the init method either
     *   1. Throws an Exception
     *   2. Does not return within a time period defined by the web container
     *
     * @param ControllerContext $config
     */
    public function init(ControllerContext $config);

    /**
     * The doFilter method of the Filter is called by the container each time a request/response pair is passed through the chain
     * due to a client request for a resource at the end of the chain. The FilterChain passed in to this method allows the Filter
     * to pass on the request and response to the next entity in the chain.
     *
     *
     */
    public function doFilter(Request $request, Response $response, FilterChain $chain);

    /**
     * Called by the web container to indicate to a filter that it is being taken out of service.
     * This method is only called once all threads within the filter's doFilter method
     * have exited or after a timeout period has passed. After the web container calls this method,
     * it will not call the doFilter method again on this instance of the filter.
     */
    public function destroy();
}