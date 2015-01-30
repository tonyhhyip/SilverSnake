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
     * Construction and instance of Statement.
     *
     * @param mysqli $link MySQLi Connection.
     * @param string $query SQL Statement.
     */
    public function __construct(\mysqli $link, $query) {
        parent::__construct($link, $query);
        $this->dbh = $link;
        $this->query = $query;
        for ($i = 0; preg_match('/[^\\]\?/', $query); $i++) {
            $query = preg_replace('::replacement[$i]::', $query, 1);
        }
        $this->stmt = $query;
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return last insert ID.
     */
    public function getInsertId() {
        return $this->insert_id;
    }

    /**
     * Binds variables to a prepared statement as parameters
     *
     * @param mixed $key Parameter key.
     * @param mixed $param Parameter value.
     */
    public function bindParam($key, $param) {
        if (is_int($key)) {
            return true;
        } elseif (is_string($key)) {
            return true;
        }
        throw new DBException('Fail to bind parameter on line ' . __LINE__ . ' of class ' . __CLASS__ . ' in ' . __FILE__);
    }
}