<?php
/**
 * @package php\db
 * package php.db
 */
namespace php\db\query;
/**
 * General class for an abstracted DELETE operation.
 */
class DeleteQuery extends Query implements QueryConditionInterface {

	/**
	 * The table from which to delete.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The condition object for this query.
	 *
	 * Condition handling is handled via composition.
	 *
	 * @var DatabaseCondition
	 */
	protected $condition;

	/**
	 * Constructs a DeleteQuery object.
	 *
	 * @param DatabaseConnection $connection
	 *   A DatabaseConnection object.
	 * @param string $table
	 *   Name of the table to associate with this query.
	 * @param array $options
	 *   Array of database options.
	 */
	public function __construct(\php\db\DbConnection $connection, $table, array $options = array()) {
		$options['return'] = Database::RETURN_AFFECTED;
		parent::__construct($connection, $options);
		$this->table = $table;

		$this->condition = new DatabaseCondition('AND');
	}

	/**
	 * Implements QueryConditionInterface::condition().
	 */
	public function condition($field, $value = NULL, $operator = NULL) {
		$this->condition->condition($field, $value, $operator);
		return $this;
	}

	/**
	 * Implements QueryConditionInterface::isNull().
	 */
	public function setIsNull($field) {
		$this->condition->setIsNull($field);
		return $this;
	}

	/**
	 * Implements QueryConditionInterface::isNotNull().
	 */
	public function setIsNotNull($field) {
		$this->condition->setIsNotNull($field);
		return $this;
	}

	/**
	 * Implements QueryConditionInterface::exists().
	 */
	public function setExists(SelectQuery $select) {
		$this->condition->setExists($select);
		return $this;
	}

	/**
	 * Implements QueryConditionInterface::notExists().
	 */
	public function setNotExists(SelectQuery $select) {
		$this->condition->setNotExists($select);
		return $this;
	}

	/**
	 * Implements QueryConditionInterface::conditions().
	 */
	public function &conditions() {
		return $this->condition->conditions();
	}

	/**
	 * Implements QueryConditionInterface::arguments().
	 */
	public function getParameter() {
		return $this->condition->arguments();
	}

	/**
	 * Implements QueryConditionInterface::where().
	 */
	public function where($snippet, $args = array()) {
		$this->condition->where($snippet, $args);
		return $this;
	}

	/**
	 * Implements QueryConditionInterface::compile().
	 */
	public function compile(\php\db\DbConnection $connection, QueryPlaceholder $queryPlaceholder) {
		return $this->condition->compile($connection, $queryPlaceholder);
	}

	/**
	 * Implements QueryConditionInterface::compiled().
	 */
	public function isCompiled() {
		return $this->condition->isCompiled();
	}

	/**
	 * Executes the DELETE query.
	 *
	 * @return
	 *   The return value is dependent on the database connection.
	 */
	public function execute() {
		$values = array();
		if (count($this->condition)) {
			$this->condition->compile($this->connection, $this);
			$values = $this->condition->arguments();
		}

		return $this->connection->query((string) $this, $values, $this->queryOptions);
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

		$query = $comments . 'DELETE FROM {' . $this->connection->escapeTable($this->table) . '} ';

		if (count($this->condition)) {

			$this->condition->compile($this->connection, $this);
			$query .= "\nWHERE " . $this->condition;
		}

		return $query;
	}
}
