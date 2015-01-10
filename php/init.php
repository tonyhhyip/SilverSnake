<?php

/**
 * @file
 * Init all the data and environment.
 */
use \php\lang;

require_once(__DIR__ .  "/php/lang/ClassLoader.php");

$default = array();

$default['classloader'] = new ClassLoader();

$_ENV = $_ENV + $default;

unset($default);

require_once(__DIR__ . "/static.php");

import("php.lang");

spl_autoload_register(function ($class) {
	$_ENV['classloader']->loadClass($class);
});

?>
