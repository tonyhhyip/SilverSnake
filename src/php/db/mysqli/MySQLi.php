<?php

namespace php\db\mysqli;
use php\db\DBException;

/**
 * Class MySQLi extending mysqli class.
 * @package php\db\mysqli
 */
class MySQLi extends \mysqli{

    /**
     * Current Database name.
     * @var string
     */
    private $dbname;

    public function __construct() {}

    /**
     * Initializes MySQLi and load the config and create the connection.
     *
     * @param array $config Connection meta data.
     */
    public function init(array $config) {
        @parent::__construct($config['host'], $config['username'], $config['password'], $config['dbname']);
        if ($this->connect_errno) {
            throw new DBException($this->connect_errno . ' ' . $this->connect_error);
        }
        $this->dbname = $config['dbname'];
    }

    /**
     * To determine the current state of autocommit use the SQL command SELECT @@autocommit.
     *
     * @return boolean current is auto commit or not.
     */
    public function isAutoCommit() {
        return intval($this->query('SELECT @@autocommit')->fetch_row()[0]) == 1;
    }

    /**
     * Initializes a statement and returns an object for use with
     *
     * @return MySQLiStatement instance of a statement.
     */
    protected function statementInit($query) {
        return new MySQLiStatement($this, $query);
    }

    /**
     * Prepare an SQL statement for execution.
     * The statement can include one or more parameter markers in the SQL statement
     * by embedding question mark (?) characters at the appropriate positions.
     *
     * @param string $query The query, as a string.
     * @return MySQLiStatement a statement object or null if an error occurred.
     */
    public function prepare($query) {
        $stmt = $this->statementInit($query);
        return $stmt;
    }

    /**
     * Performs a query against the database.
     *
     * @param string $query The query string.
     * @return MySQLiStatement For successful queries will return a MySQLiResult object.
     */
    public function query($query) {
        $stmt = $this->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Rolls back current transaction
     *
     */
    public function rollBack() {
        return parent::rollback();
    }

    /**
     * Escapes special characters in a string for use in an SQL statement,
     * taking into account the current charset of the connection.
     * Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
     *
     * @param string $query The string to be escaped.
     * @return string an escaped string.
     */
    public function escape($query) {
        return $this->escape_string($query);
    }

    /**
     * Returns a list of errors from the last command executed
     *
     * @return array a list of errors
     */
    public function getErrorList() {
        return $this->error_list;
    }

    /**
     * Returns the error code for the most recent function call
     *
     * @return int error code
     */
    public function getErrorCode() {
        return $this->errno;
    }

    /**
     * Returns a string description of the last error.
     *
     * @return string description
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Get the name of database being connected.
     *
     * @return string Database name.
     */
    public function getDbName() {
        return $this->dbname;
    }

    /**
     * Select fields from a table.
     * Condition array should contains an array specific of
     * field, value and operator.
     *
     * @param string $table Name of table.
     * @param array $fields An array of the name of fields.
     * @param string $condition condition to select.
     * @param int $limit Limit of number of row
     * @return MySQLiStatement result of the query.
     */
    public function select($table, array $fields = array('*'), $condition = '', $limit = -1) {
        $sql = "SELECT ";
        $sql .= count($fields) == 1 && $fields[0] = '*' ? '*' : implode(',', array_map(function ($x) {
                return MySQLiStatement::escapeField($x);
            }, $fields));
        $sql .= ' FROM ' . MySQLiStatement::escapeField($table);
        if (count($condition)) {
            $sql .= ' WHERE $condition';
        }
        if ($limit > 0) {
            $sql .= ' LIMIT $limit';
        }
        return $this->query($sql);
    }
}