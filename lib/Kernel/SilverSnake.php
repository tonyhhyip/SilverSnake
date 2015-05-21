<?php

namespace SilverSnake\Kernel;

use SilverSnake\Controller\ExceptionController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use SilverSnake\Event\ExceptionResponseEvent;
use SilverSnake\Event\RequestEvent;
use SilverSnake\Event\ResponseEvent;
use SilverSnake\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\TerminableInterface;

class SilverSnake implements HttpKernelInterface, TerminableInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $stack;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @var \SilverSnake\Kernel\ControllerLoader
     */
    private $matcher;

    /**
     * @var array
     */
    private $error = array();

    private static function loadListener(EventDispatcher $dispatcher)
    {
        $content = file_get_contents(__DIR__ . '/../../config/listener.json', true);
        $content = (array)json_decode($content);
        foreach ($content as $listener) {
            $dispatcher->addListener($listener->event, array($listener->class, $listener->method));
        }
    }

    private static function loadError()
    {
        $content = file_get_contents(__DIR__ . '/../../config/error.json', true);
        $content = (array)json_decode($content);
        $errors = array();
        foreach ($content as $error) {
            if ($error->class instanceof ExceptionController)
                $errors[$error->statusCode] = $error->class;
        }
        return $errors;
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->stack = new RequestStack();
        $this->dispatcher = new EventDispatcher();
        self::loadListener($this->dispatcher);
        $this->matcher = new ControllerLoader();
        $this->error = self::loadError();
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        $request->headers->set('X-Php-Ob-Level', ob_get_level());

        try {
            return $this->handleRaw($request, $type);
        } catch (\Exception $e) {
            if (false === $catch) {
                $this->finishRequest();
                throw $e;
            }

            return $this->handleException($e, $request);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function terminate(Request $request, Response $response)
    {
        $this->dispatcher->dispatch(KernelEvents::TERMINATE, new PostResponseEvent($this, $request, $response));
    }

    /**
     * @param Request $request
     * @param int $type
     */
    private function handleRaw(Request $request, $type)
    {
        $this->stack->push($request);

        if ($type === HttpKernelInterface::MASTER_REQUEST) {
            $event = new RequestEvent($this, $request);
            $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);
        }

        $controller = $this->matcher->getController($request);
        if (null === $controller) {
            throw new NotFoundException(sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }

        $controller->init();
        $request->request->replace($this->matcher->getArguments($request, array($controller, 'service')));
        $response = $controller->service($request);
        $controller->destroy();

        $this->dispatcher->dispatch(KernelEvents::RESPONSE, new ResponseEvent($this, $response));

        $this->stack->pop();

        return $response;
    }

    private function finishRequest()
    {
        $this->stack->pop();
    }

    private function handleException(HttpException $e, Request $request)
    {
        $event = new ExceptionResponseEvent($this, $request, $e);
        $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);

        $controllerClass = $this->error[$e->getStatusCode()];
        $controller = new $controllerClass($e);
        if (!$controller instanceof ExceptionController) {
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $controller->init();
        $response = $controller->service($request);
        $controller->destroy();
        return $response;
    }
}