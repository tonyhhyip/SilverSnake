<?php

/**
 * @file Function and constants of the Config.
 */

/**
 * Loads the persistent variable table.
 *
 * The config table is composed of values that have been saved in the table
 * with conf_set() as well as those explicitly specified in the
 * configuration file.
 */
function conf_initalize($config = array()) {
	global $conf;
	$conf = new \php\conf\Properties();
	foreach ($config as $key => $val) {
		$conf->setProperty($key, $val);
	}
}

/**
 * Returns a persistent variable.
 *
 * Case-sensitivity of the variable_* functions depends on the database
 * collation used. To avoid problems, always use lower case for persistent
 * variable names.
 *
 * @param $name
 *   The name of the variable to return.
 * @param $default
 *   The default value to use if this variable has never been set.
 *
 * @return
 *   The value of the variable. Unserialization is taken care of as necessary.
 *
 * @see conf_del()
 * @see conf_set()
 */
function conf_get($name, $default = NULL) {
	global $conf;
	
	if (!isset($conf)) {
		conf__initialize();
	}
	
	return $conf instanceof \php\conf\Properties ? $conf->getProperties($name) :
			isset($conf[$name]) ? $conf[$name] : $default;
}



?>
