<?php

/**
 * @file
 * Static function and constants.
 * @version v 0.0.1
 */

/**
 * Path to SilverSnake
 */
define('SilverSnake', __DIR__);


function import(string $package):void {
	$path = str_replace("\\", "/", $package);
	$path = str_replace(".", "/", $path);
	$path = SilverSnake . "/$path";
	$files = scandir($path);
	$files = array_diff($files, array(".", ".."));

	foreach($files as $file) {
		$_ENV['classloader']->defineClass($package . "." . basename($file, ".php"), $path . "/$file");
	}
	
	if (file_exists('$path/static.php'))	
		include_once('$path/static.php');
	if (file_exists('$path/init.php'))
		include_once('$path/init.php');
}
?>
