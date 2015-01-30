<?php

namespace php\db\mysqli;


use php\db\DBException;

class MySQLiStatement extends \mysqli_stmt{

    /**
     * Connection of MySQLi.
     *
     * @var mysqli
     */
    protected $dbh;

    /**
     * SQL Statement.
     *
     * @var string
     */
    protected $query;

    /**
     * SQL Statement with parameter.
     *
     * @var string
     */
    protected $stmt;

    /**
     * Parameter to be used when execute.
     *
     * @var array
     */
    protected $param = array();

    /**
     * Construction and instance of Statement.
     *
     * @param mysqli $link MySQLi Connection.
     * @param string $query SQL Statement.
     */
    public function __construct(\mysqli $link, $query) {
        parent::__construct($link, $query);
        $this->dbh = $link;
        $this->query = $query;
        $this->stmtInit();
    }

    /**
     * Ready for the statement.
     */
    private function stmtInit() {
        $query = $this->query;
        for ($i = 0; preg_match('/[^\\]\?/', $query); $i++) {
            $query = preg_replace('::replacement[$i]::', $query, 1);
            $this->param[$i] = null;
        }
        $this->stmt = $query;
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int last insert ID.
     */
    public function getInsertId() {
        return $this->insert_id;
    }

    /**
     * Frees stored result memory for the given statement handle
     *
     */
    public function freeResult() {
        $this->free_result();
    }

    /**
     * Resets a prepared statement on client and server to state after prepare.
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function reset() {
        if (parent::reset())
            return false;
        $this->stmtInit();
        return true;
    }

    /**
     * Get SQL statement.
     *
     * @return string SQL Statement.
     */
    public function getStatement() {
        return $this->query;
    }

    /**
     * Executes a prepared Query.
     *
     * @throws DBException if any error happened
     */
    public function execute() {
        parent::execute();
        if ($this->errno) {
            throw new DBException('Fail to execute query ' . $this->getStatement()
                . ' in ' . __CLASS__ . ' on line ' . __LINE__ . ' in ' . __FILE__ . '\n'
                . ' catch error of ' . $this->errno . ': ' . $this->error
            );
        }

    }

    /**
     * Get the result of the statement after execute.
     *
     * @return MySQLiResult object instance
     */
    public function getResult() {
        return new MySQLiResult($this->get_result());
    }

    /**
     * Get the number of row being affected.
     *
     * @return int number of rows
     */
    public function getAffectedRow() {
        return $this->affected_rows;
    }

    /**
     * Returns the number of rows affected by the last SQL statement.
     *
     * @return int number of rows
     */
    public function getRowCount() {
        return $this->num_rows;
    }

    /**
     * Escape the name of field.
     *
     * @param string $field name of field.
     * @return string field after escape.
     */
    public static function escapeField($field) {
        return '`$field`';
    }
}