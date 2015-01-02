<?php

/**
 * @package php\db
 * package php.db;
 */
namespace php\db\query;

/**
 * Interface for a query that accepts placeholders.
 */
interface QueryPlaceholder {
	/**
	 * Returns a unique identifier for this object.
	 */
	public function uniqueIdentifier();
	
	/**
	 * Returns the next placeholder ID for the query.
	 *
	 * @return
	 *   The next available placeholder ID as an integer.
	*/
	public function nextPlaceholder();
}

?>