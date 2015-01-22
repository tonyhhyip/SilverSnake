<?php
/**
 * @package php.db
 */

namespace php\db;

/**
 * Connection interface.
 * Driver connections must implement this interface.
 *
 * This resembles (a subset of) the PDO interface.
 *
 */
interface ConnectionInterface {
    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @param string $prepareString
     *
     * @return \php\db\Statement
     */
    function prepare($prepareString);

    /**
     * Executes an SQL statement, returning a result set as a Statement object.
     *
     * @return \php\db\Statement
     */
    function query();

    /**
     * Quotes a string for use in a query.
     *
     * @param string  $input
     * @param integer $type
     *
     * @return string
     */
    function quote($input, $type=\PDO::PARAM_STR);

    /**
     * Executes an SQL statement and return the number of affected rows.
     *
     * @param string $statement
     *
     * @return integer
     */
    function exec($statement);

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string|null $name
     *
     * @return string
     */
    function lastInsertId($name = null);

    /**
     * Initiates a transaction.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    function beginTransaction();

    /**
     * Commits a transaction.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    function commit();

    /**
     * Rolls back the current transaction, as initiated by beginTransaction().
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    function rollBack();

    /**
     * Returns the error code associated with the last operation on the database handle.
     *
     * @return string|null The error code, or null if no operation has been run on the database handle.
     */
    function errorCode();

    /**
     * Returns extended error information associated with the last operation on the database handle.
     *
     * @return array
     */
    function errorInfo();
}