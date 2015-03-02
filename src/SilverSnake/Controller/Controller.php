<?php

namespace SilverSnake\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller {

    private static function noService(Request $request, Response $response) {
        $response->setStatusCode(405);
    }

    public function service(Request $request, Response $response){
        $method = $request->getMethod();
        $method = strtolower($method);
        $method = ucfirst($method);
        $method = 'do' . $method;
        $this->method($request, $response);
    }

    protected function doGet(Request $request, Response $response) {
        self::noService($request, $response);
    }

    protected function doPost(Request $request, Response $response) {
        self::noService($request, $response);
    }

    protected function doPut(Request $request, Response $response) {
        self::noService($request, $response);
    }

    protected function doHead(Request $request, Response $response) {
        self::noService($request, $response);
    }

}