<?php

/**
 * @package \php\conf
 * package php.conf
 */

namespace \php\conf;

class Properties {
	
	/**
	 * @var Properties List
	 */
	private $conf = array();
	
	/**
	 * @var The defaults
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
	 * @param $key
	 *   The property key.
	 * @return The value in this property list with specified key value.
	 */
}

?>
