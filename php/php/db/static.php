<?php

/**
 * @file Function and constants of the Config.
 */

/**
 * An old days function to escape sql function.
 * Not Suggest to use.
 * 
 * @param mixed $value
 *			value of the sql.
 * @param string $type
 *			Type of data.
 * @param string $definedValue
 *			value of defined value
 * @param string $notDefineValue
 *			Value for not define.
 * @return string The escaped sql.
 */
function SQLformatString($value, $type, $definedValue = "", $notDefinedValue=""){
	if(PHP_VERSION < 6){
		$value = get_magic_quotes_gpc() ? stripslashes($value) : $value;
	}
	$value = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($value) : mysql_escape_string($value);
	switch($type){
		case 'search':
			$value = $value != "" ?"%" . $value . "%" : "NULL";
		case "text":
		case "date":
			$value = $value != "" ? "'" . $value . "'" : "NULL";
		break;    
		case "long":
		case "int":
			$value = $value != "" ? intval($value) : "NULL";
		break;
		case "double":
			$value = $value != "" ? doubleval($value) : "NULL";
		break;
		case "defined":
			$value = $value!="" ? $definedValue : $notDefinedValue;
		break;
	}
	return $value;
}
?>