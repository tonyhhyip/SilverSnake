<?php

namespace SilverSnake\Kernel;


use Psr\Log\LoggerInterface;
use SilverSnake\Controller\Controller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class ControllerLoader implements ControllerResolverInterface
{
    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $collection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    /**
     * constructor
     *
     * @param string $source
     */
    public function __constructor(LoggerInterface $logger = null, $source = 'routes.yml')
    {
        $this->logger = $logger;
        $locator = new FileLocator(__DIR__ . '/../../config');
        $loader = new YamlFileLoader($locator);
        $this->collection = $loader->load($source);
    }

    /**
     * @param Request $request
     *
     * @return Controller
     */
    public function getController(Request $request)
    {
        $context = new RequestContext($request->getBaseUrl());
        $collection = $this->getCollection();
        $matcher = new UrlMatcher($collection, $context);
        try {
            $meta = $matcher->matchRequest($request);
            $controller = new $meta['controller']();
            if (!$controller instanceof Controller)
                throw new ResourceNotFoundException();
            return $controller;
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param Request $request
     * @param callable $controller
     *
     * @return array
     */
    public function getArguments(Request $request, $controller)
    {
        $context = new RequestContext($request->getBaseUrl());
        $matcher = new UrlMatcher($this->collection, $context);
        try {
            $meta = $matcher->matchRequest($request);
            return $meta;
        } catch (ResourceNotFoundException $e) {
            return array();
        }
    }

    /**
     * @return RouteCollection
     */
    public function getCollection()
    {
        return is_null($this->collection) ? new RouteCollection() : $this->collection;
    }
}