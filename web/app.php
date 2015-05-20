<?php

use Symfony\Component\HttpFoundation\Request;
use SilverSnake\Kernel\SilverSnake;

require_once __DIR__ . '/../bootstrap/autoload.php';

$kernel = new SilverSnake();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);