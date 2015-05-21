<?php


namespace SilverSnake\Exception;


use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct($message = null, Exception $previous = null, array $header = array()){
        parent::__construct(Response::HTTP_NOT_FOUND, $message, $previous, $header);
    }
}