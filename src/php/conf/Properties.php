<?php

/**
 * @package \php\conf
 * package php.conf
 * @license Apache License 2.0
 */

namespace \php\conf;

class Properties {
	
	/**
	 * @var array
	 */
	private $conf = array();
	
	/**
	 * @var Properties
	 */
	private $defaults;
	
	/**
	 * Create a empty property list with defaults.
	 * 
	 * @param The defaults
	 */
	public function __construct(\php\conf\Properties $default = null) {
		$this->defaults = $defaults;
	}
	
	/**
	 * Searches for the property with the specified key in the property list.
	 * If the key is not found in the property list, the default property list,
	 * and its defaults, recursively, are then checked.
	 * The method returns NULL if the property is not found.
	 * 
	 * @param string $key
	 *   The property key.
	 * @param string $defaultValue
	 * 	 a default value
	 * @return The value in this property list with specified key value.
	 * @see Properties#setProperty(string, string)
	 */
	public function getProperty(string $key, string $defaultValue = null) {
		$val = $this->conf[$key];
		if (!is_string($val) && $val != null) {
			$val = $val . "";
		}
		if ($val == null && $this->defaults != null) {
			return $this->defaults->getProperty($key);
		} elseif ($defaultValue != null) {
			return $defaultValue;	
		} else {
			return $val;
		}
	}
	
	/**
	 * Set the value of the Property with specified key.
	 * 
	 * @param string $key
	 * 			The key to be placed into this property list.
	 * @param string $value
	 * 			the value corresponding to key.
	 * @see getProperty(string, string)
	 */
	public function setProperty(string $key, string $value) {
		if (!is_string($key) || !is_string($value)) {
			$key .= "";
			$value .= "";
		}
		
		$this->conf[$key] = $value;
	}
	
	/**
	 * Returns an enumeration of all the keys in this property list,
	 * including distinct keys in the default property list if a key
	 * of the same name has not already been found from the main
	 * properties list.
	 * 
	 * @return an array of all the keys in this property list, including
	 * 			the keys in the default property list.
	 */
	public function propertyNames() {
		return array_keys($this->conf) +
			($this->defaults == null ? array() : $this->defaults->propertyNames()); 
	}
}

?>
