<?php

namespace SilverSnake\Test\Logger;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MonologTest extends \PHPUnit_Framework_TestCase {

    public function testStreamLog()
    {
        $logger = new Logger('logger');

        $handler = new StreamHandler(__DIR__ . '/../../app/log/app.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $logger->addDebug('Test');
    }
}
