<?php

namespace Kernel;


use SilverSnake\Kernel\SilverSnake;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SilverSnakeTest extends \PHPUnit_Framework_TestCase {

    public function testConstruction()
    {
        $kernel = new SilverSnake();
        $this->assertTrue($kernel instanceof HttpKernelInterface);
    }
}
