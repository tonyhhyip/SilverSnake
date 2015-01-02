<?php

/**
 * @package \php\lang
 * @file static function and constants of the package.
 */

/**
 * Print a line with a new line(\n) at the end.
 * @param string $str String to output.
 */
function println(string $str) {
	print($str);
	print "\n";
}