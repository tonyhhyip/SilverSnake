<?php
/**
 * SilverSnake
 */

namespace SilverSnake\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ExceptionController extends ContextController
{
    /**
     * @var \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private $exception;

    public function __construct(HttpException $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @param Request $request
     */
    public function service(Request $request)
    {
        $response = $this->doGet($request);
        $response->setStatusCode($this->exception->getStatusCode());
        $response->headers->replace($this->exception->getHeaders());
    }

}