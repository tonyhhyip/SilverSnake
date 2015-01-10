<?php

/**
 * @file Function and constants of the Config.
 */

function SQLformatString($theValue, theType, $theDefinedValue="",$theNotDefinedValue=""){
	if(PHP_VERSION < 6){
		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	}
	$theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	switch($theType){
		case 'search':
			$theValue=($theValue!="")?"%" . $theValue . "%" : "NULL";
		case "text":case "date":
			$theValue=($theValue!="")?"'" . $theValue . "'" : "NULL";
		break;    
		case "long":
		case "int":
			$theValue=($theValue!="") ? intval($theValue) : "NULL";
		break;
		case "double":
			$theValue=($theValue!="") ? doubleval($theValue) : "NULL";
		break;
		case "defined":
			$theValue=($theValue!="") ? $theDefinedValue : $theNotDefinedValue;
		break;
	}
	return $theValue;
}
?>