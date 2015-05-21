<?php

namespace { $loader = require_once __DIR__ . '/../vendor/autoload.php';}

namespace SilverSnake {
    use Composer\Autoload\ClassLoader;

    $loader = new ClassLoader();
    $app = dirname(__DIR__);
    $loader->setPsr4('SilverSnake\\', array($app . '/lib'));
    $loader->setPsr4('SilverSnake\\Test\\', array($app . '/test'));

    $config = parse_ini_file($app . '/config/app.ini', true);
    if (array_key_exists('autoload', $config)) {
        $config = $config['autoload'];
        $prefix = str_replace('::', '\\', $config['prefix'] . '::');
        $loader->setPsr4($prefix, $app . $config['dist']);
    }

    $loader->register();
}