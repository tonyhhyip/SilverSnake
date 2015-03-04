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

/**
 * Get the char at index position in the string str.
 * @param string $str
 *		The string
 * @param int $index
 *		The index.
 * @return the char.
 */
function charAt($str,$index){
	return substr($str,$index,1);
}

/**
 * Functions like substring in Java or Javascript.
 * @param string $str
 * @param int start
 * @param int $end
 * @return string string after substring.
 */
function substring($str,$start,$end){
	if($start > $end){
		$tmp = $end;
		$end = $start;
		$start = $tmp;
		unset($tmp);
	}
	return substr($str, $start, $end - $start);
}