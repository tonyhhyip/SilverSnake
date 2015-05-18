<?php

namespace SilverSnake\Filter;


use SilverSnake\Http\Filter;
use SilverSnake\Http\Filter\FilterChain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterChainHandler implements FilterChain {

    /**
     * @var array<Filter>
     */
    private $filter = array();

    /**
     * @var array<Filter>
     */
    private $filterQueue = array();

    /**
     * @var Controller
     */
    private $controller;

    public function __construct(Controller $controller, array $filters = array()) {
        $this->controller = $controller;
        $this->filter = $filters;
        $this->filterQueue = $filters;
    }

    public function doFilter(Request $request, Response $response) {
        if (count($this->filterQueue)) {
            $filter = array_shift($this->filterQueue);
            if ($filter instanceof Filter) {
                $filter->init($this->controller->getControllerContext());
                $filter->doFilter($request, $response, $this);
                $filter->destroy();
            }
        } else {
            $this->controller->service($request, $response);
        }
    }
}