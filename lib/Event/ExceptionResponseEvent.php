<?php

namespace SilverSnake\Event;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionResponseEvent extends ResponseEvent
{
    /**
     * @var \Exception
     */
    private $exception;


    public function __construct(HttpKernelInterface $kernel, Request $request, Exception $e)
    {
        parent::__construct($kernel, null);
        $this->setException($e);
    }

    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }
}