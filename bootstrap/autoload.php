<?php

namespace { $loader = require_once __DIR__ . '/../vendor/autoload.php';}

namespace SilverSnake {
    use Composer\Autoload\ClassLoader;

    $loader = new ClassLoader();
    $app = dirname(__DIR__);
    $loader->addPsr4('SilverSnake', $app . '/lib');
    $loader->addPsr4('SilverSnake\Test', $app . '/test', true);

    $config = parse_ini_file($app . '/config/app.ini');
    $loader->addPsr4($config['prefix'], $app . $config['dist']);

    $loader->register();
}