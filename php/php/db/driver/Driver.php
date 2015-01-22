<?php
/**
 * @package php.db.driver;
 */

namespace php\db\driver;

use \php\db\Connection;

/**
 * Driver interface.
 * Interface that all DBAL drivers must implement.
 *
 */
interface Driver {
    /**
     * Attempts to create a connection with the database.
     *
     * @param array       $params        All connection parameters passed by the user.
     * @param string|null $username      The username to use when connecting.
     * @param string|null $password      The password to use when connecting.
     * @param array       $driverOptions The driver options to use when connecting.
     *
     * @return \php\db\Connection The database connection.
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array());

    /**
     * Gets the DatabasePlatform instance that provides all the metadata about
     * the platform this driver connects to.
     *
     * @return \php\db\platform\AbstractPlatform The database platform.
     */
    public function getDatabasePlatform();

    /**
     * Gets the SchemaManager that can be used to inspect and change the underlying
     * database schema of the platform this driver connects to.
     *
     * @param \php\db\Connection $conn
     *
     * @return \php\db\schema\AbstractSchemaManager
     */
    public function getSchemaManager(Connection $conn);

    /**
     * Gets the name of the driver.
     *
     * @return string The name of the driver.
     */
    public function getName();

    /**
     * Gets the name of the database connected to for this driver.
     *
     * @param \php\db\Connection $conn
     *
     * @return string The name of the database.
     */
    public function getDatabase(Connection $conn);
}