<?php

/**
 * @package php\db
 * package php.db;
 */

namespace php\db;

/**
 * Represents a prepared statement.
 */

interface DatabaseStatementInterface extends \Traversable {
	/**
	 * Executes a prepared statement
	 *
	 * @param $args
	 *   An array of values with as many elements as there are bound parameters in
	 *   the SQL statement being executed.
	 * @param $options
	 *   An array of options for this query.
	 *
	 * @return
	 *   TRUE on success, or FALSE on failure.
	 */
	public function execute($args = array(), $options = array());
	
	/**
	 * Gets the query string of this statement.
	 *
	 * @return
	 *   The query string, in its form with placeholders.
	*/
	public function getQueryString();
	
	/**
	 * Returns the number of rows affected by the last SQL statement.
	 *
	 * @return
	 *   The number of rows affected by the last DELETE, INSERT, or UPDATE
	 *   statement executed.
	*/
	public function rowCount();
	
		
	/**
	 * Returns a single field from the next record of a result set.
	 *
	 * @param $index
	 *   The numeric index of the field to return. Defaults to the first field.
	 *
	 * @return
	 *   A single field from the next record, or FALSE if there is no next record.
	*/
	public function fetchField($index = 0);
	
	/**
	 * Fetches the next row and returns it as an associative array.
	 *
	 * This method corresponds to PDOStatement::fetchObject(), but for associative
	 * arrays. For some reason PDOStatement does not have a corresponding array
	 * helper method, so one is added.
	 *
	 * @return
	 *   An associative array, or FALSE if there is no next row.
	*/
	public function fetchAssoc();
	
	/**
	 * Returns an entire single column of a result set as an indexed array.
	 *
	 * Note that this method will run the result set to the end.
	 *
	 * @param $index
	 *   The index of the column number to fetch.
	 *
	 * @return
	 *   An indexed array, or an empty array if there is no result set.
	*/
	public function fetchCol($index = 0);
	
	/**
	 * Returns the entire result set as a single associative array.
	 *
	 * This method is only useful for two-column result sets. It will return an
	 * associative array where the key is one column from the result set and the
	 * value is another field. In most cases, the default of the first two columns
	 * is appropriate.
	 *
	 * Note that this method will run the result set to the end.
	 *
	 * @param $key_index
	 *   The numeric index of the field to use as the array key.
	 * @param $value_index
	 *   The numeric index of the field to use as the array value.
	 *
	 * @return
	 *   An associative array, or an empty array if there is no result set.
	*/
	public function fetchAllKeyed($key_index = 0, $value_index = 1);
	
	/**
	 * Returns the result set as an associative array keyed by the given field.
	 *
	 * If the given key appears multiple times, later records will overwrite
	 * earlier ones.
	 *
	 * @param $key
	 *   The name of the field on which to index the array.
	 * @param $fetch
	 *   The fetchmode to use. If set to PDO::FETCH_ASSOC, PDO::FETCH_NUM, or
	 *   PDO::FETCH_BOTH the returned value with be an array of arrays. For any
	 *   other value it will be an array of objects. By default, the fetch mode
	 *   set for the query will be used.
	 *
	 * @return
	 *   An associative array, or an empty array if there is no result set.
	*/
	public function fetchAllAssoc($key, $fetch = NULL);
}
?>