<?php

/**
 * @package php\db
 * package php.db;
 */

namespace php\db\query;

/**
 * General class for an abstracted INSERT query.
 */
class InsertQuery extends Query {
	/**
	 * The table on which to insert.
	 *
	 * @var string
	 */
	protected $table;
	
	/**
	 * An array of fields on which to insert.
	 *
	 * @var array
	 */
	protected $insertFields = array();
	
	/**
	 * An array of fields that should be set to their database-defined defaults.
	 *
	 * @var array
	*/
	protected $defaultFields = array();
	
	/**
	 * A nested array of values to insert.
	 *
	 * $insertValues is an array of arrays. Each sub-array is either an
	 * associative array whose keys are field names and whose values are field
	 * values to insert, or a non-associative array of values in the same order
	 * as $insertFields.
	 *
	 * Whether multiple insert sets will be run in a single query or multiple
	 * queries is left to individual drivers to implement in whatever manner is
	 * most appropriate. The order of values in each sub-array must match the
	 * order of fields in $insertFields.
	 *
	 * @var array
	 */
	protected $insertValues = array();
	
	/**
	 * A SelectQuery object to fetch the rows that should be inserted.
	 *
	 * @var SelectQuery
	 */
	protected $fromQuery;
	
	/**
	 * Constructs an InsertQuery object.
	 *
	 * @param DatabaseConnection $connection
	 *   A DbConnection object.
	 * @param string $table
	 *   Name of the table to associate with this query.
	 * @param array $options
	 *   Array of database options.
	 */
	public function __construct(\php\db\DbConnection $connection, $table, array $options = array()) {
		if (!isset($options['return'])) {
      		$options['return'] = Database::RETURN_INSERT_ID;
    	}
    	parent::__construct($connection, $options);
    	$this->table = $table;
	}
	
	/**
	 * Adds a set of field->value pairs to be inserted.
	 *
	 * This method may only be called once. Calling it a second time will be
	 * ignored. To queue up multiple sets of values to be inserted at once,
	 * use the values() method.
	 *
	 * @param $fields
	 *   An array of fields on which to insert. This array may be indexed or
	 *   associative. If indexed, the array is taken to be the list of fields.
	 *   If associative, the keys of the array are taken to be the fields and
	 *   the values are taken to be corresponding values to insert. If a
	 *   $values argument is provided, $fields must be indexed.
	 * @param $values
	 *   An array of fields to insert into the database. The values must be
	 *   specified in the same order as the $fields array.
	 *
	 */
	public function fields(array $fields, array $values = array()) {
		if (empty($this->insertFields)) {
			if (empty($values)) {
				if (!is_numeric(key($fields))) {
					$values = array_values($fields);
					$fields = array_keys($fields);
				}
			}
			$this->insertFields = $fields;
			if (!empty($values)) {
				$this->insertValues[] = $values;
			}
		}
	}
	
	/**
	 * Adds another set of values to the query to be inserted.
	 *
	 * If $values is a numeric-keyed array, it will be assumed to be in the same
	 * order as the original fields() call. If it is associative, it may be
	 * in any order as long as the keys of the array match the names of the
	 * fields.
	 *
	 * @param $values
	 *   An array of values to add to the query.
	 *
	 */
	public function setValues(array $value) {
		if (is_numeric(key($value))) {
			$this->insertValues[] = $value;
		} else {
			// Reorder the submitted values to match the fields array.
			foreach ($this->insertFields as $key) {
				$insertValue[$key] = $values;
			}
			// For consistency, the values array is always numerically indexed.
			$this->insertValues[] = array_values($insertValue);
		}
	}
	
	/**
	 * Specifies fields for which the database defaults should be used.
	 *
	 * If you want to force a given field to use the database-defined default,
	 * not NULL or undefined, use this method to instruct the database to use
	 * default values explicitly. In most cases this will not be necessary
	 * unless you are inserting a row that is all default values, as you cannot
	 * specify no values in an INSERT query.
	 *
	 * Specifying a field both in fields() and in useDefaults() is an error
	 * and will not execute.
	 *
	 * @param $fields
	 *   An array of values for which to use the default values
	 *   specified in the table definition.
	 */
	public function setDefaults(array $fields) {
		$this->defaultFields = $fields;
	}
	
	/**
	 * Sets the fromQuery on this InsertQuery object.
	 *
	 * @param SelectQuery $query
	 * The query to fetch the rows that should be inserted.
	 */
	public function setFormQuery(SelectQuery $query) {
		$this->fromQuery = $query;
	}
	
	/**
	 * Executes the insert query.
	 *
	 * @return
	 *   The last insert ID of the query, if one exists. If the query
	 *   was given multiple sets of values to insert, the return value is
	 *   undefined. If no fields are specified, this method will do nothing and
	 *   return NULL. That makes it safe to use in multi-insert loops.
	 * @throws SQLException
	 */
	public function execute() {
		// If validation fails, simply return NULL. Note that validation routines
		// in preExecute() may throw exceptions instead.
		if (!$this->preExecute()) {
			return null;
		}
		
		// If we're selecting from a SelectQuery, finish building the query and
		// pass it back, as any remaining options are irrelevant.
		if (!empty($this->fromQuery)) {
			$sql = (string) $this;
			// The SelectQuery may contain arguments, load and pass them through.
			return $this->connection->query($sql, $this->fromQuery->getArguments(), $this->queryOptions);
		}
		
		$lastInsertId = 0;
		
		// Each insert happens in its own query in the degenerate case. However,
		// we wrap it in a transaction so that it is atomic where possible. On many
		// databases, such as SQLite, this is also a notable performance boost.
		$transaction = $this->connection->startTransaction();
		
		try {
			$sql = (string) $this;
			foreach ($this->insertValues as $insertValue) {
				$lastInsertId = $this->connection->query($sql, $insertValue, $this->queryOptions);
			}
		} catch (\Exception $e) {
			$transaction->rollback();
			throw new \php\db\SQLException($e->getMessage(), $e->getCode(), $e);
		}
		
		// Re-initialize the values array so that we can re-use this query.
		$this->insertValues = array();
		
		// Transaction commits here where $transaction looses scope.
		
		return $lastInsertId;
	}
	
	/**
	 * Implements PHP magic __toString method to convert the query to a string.
	 *
	 * @return string
	 *   The prepared statement.
	 */
	public function __toString() {
		// Create a sanitized comment string to prepend to the query.
		$comments = $this->connection->makeComment($this->comments);
		
		// Default fields are always placed first for consistency.
		$insertFields = $this->insertFields + $this->defaultFields;
		
		if (!empty($this->formQuery)) {
			return $comments . "INSERT INTO {" . $this->table . "} (" . implode(', ', $insertFields) . ' ) ' . $this->fromQuery;
		}
		
		// For simplicity, we will use the $placeholders array to inject
		// default keywords even though they are not, strictly speaking,
		// placeholders for prepared statements.
		$placeholder = array();
		$placeholder = array_pad($placeholder, count($this->defaultFields), 'default');
		$placeholder = array_pad($placeholder, count($this->insertFields), '?');
		
		return $comments . 'INSERT INTO {' . $this->table . '} (' . implode(', ', $insert_fields) . ') VALUES (' . implode(', ', $placeholders) . ')';
	}
	
	/**
	 * Preprocesses and validates the query.
	 *
	 * @return
	 *   TRUE if the validation was successful, FALSE if not.
	 *
	 * @throws SQLException
	 */
	public function preExecute() {
		// Confirm that the user did not try to specify an identical
		// field and default field.
		if (array_intersect($this->insertFields, $this->defaultFields)) {
			throw new SQLException('You may not specify the same field to have a value and a schema-default value.');
		}
		if (!empty($this->fromQuery)) {
			// We have to assume that the used aliases match the insert fields.
			// Regular fields are added to the query before expressions, maintain the
			// same order for the insert fields.
			// This behavior can be overridden by calling fields() manually as only the
			// first call to fields() does have an effect.
			$this->fields(array_merge(array_keys($this->fromQuery->getFields()), array_keys($this->fromQuery->getExpressions())));
		} else {
			// Don't execute query without fields.
			if (count($this->insertFields) + count($this->defaultFields) == 0) {
				throw new SQLException('There are no fields available to insert with.');
			}
		}
		
		// If no values have been added, silently ignore this query. This can happen
		// if values are added conditionally, so we don't want to throw an
		// exception.
		return isset($this->insertValues[0]) && count($this->insertFields) <= 0 && !empty($this->fromQuery);
	}
}

?>