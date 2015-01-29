<?php

namespace php\db\mysqli;
use php\db\DBException;

/**
 * Class MySQLi extending mysqli class.
 * @package php\db\mysqli
 */
class MySQLi extends \mysqli{
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
    }

    /**
     * To determine the current state of autocommit use the SQL command SELECT @@autocommit.
     *
     * @return current is auto commit or not.
     */
    public function isAutoCommit() {
        return intval($this->query('SELECT @@autocommit')->fetch_row()[0]) == 1;
    }

    /**
     * Initializes a statement and returns an object for use with
     *
     * @return instance of a statement.
     */
    protected function statementInit($query) {
        return new MySQLiStatement($this, $query);
    }

    public function stmt_init() {
        return $this->statementInit();
    }

    /**
     * Prepare an SQL statement for execution.
     * The statement can include one or more parameter markers in the SQL statement
     * by embedding question mark (?) characters at the appropriate positions.
     *
     * @param string $query The query, as a string.
     * @return a statement object or null if an error occurred.
     */
    public function prepare($query) {
        $stmt = $this->statementInit($query);
        return $stmt;
    }

    /**
     * Performs a query against the database.
     *
     * @param string $query The query string.
     * @return For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries will return a MySQLiResult object.
     *          For other successful queries will return TRUE.
     */
    public function query($query) {
        $stmt = $this->prepare($query);
        $stmt->execute();
        return $stmt->getResult();
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
     * @return an escaped string.
     */
    public function escape($query) {
        return $this->escape_string($query);
    }
}