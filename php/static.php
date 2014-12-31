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

/**
 * Minimum supported version of PHP
 */
define('SILVERSNAKE_MIMIMUM_PHP', '5.3.0');

/**
 * Load the package into the default ClassLoader and execute the static and init script.
 * @param string $package
 */
function import($package) {
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

/**
 * Print a line with a new line(\n) at the end.
 * @param string $str String to output.
 */
function println(string $str) {
	print($str);
	print "\n";
}

?>
