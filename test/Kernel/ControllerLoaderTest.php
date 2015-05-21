<?php
/**
 * SilverSnake
 */

namespace SilverSnake\Test\Kernel;


use SilverSnake\Kernel\ControllerLoader;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\RouteCollection;

class ControllerLoaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \SilverSnake\Kernel\ControllerLoader
     */
    private $loader;

    public function testConstruct()
    {
        $loader = new ControllerLoader();
        $this->loader = $loader;

        $this->assertTrue($loader instanceof ControllerResolverInterface);
    }

    /**
     * @depends testConstruct
     */
    public function testGetCollection()
    {
        $loader = new ControllerLoader();
        $collection = $loader->getCollection();
        $this->assertTrue($collection instanceof RouteCollection);
    }

}
