<?php
/**
 * @package php.db;
 */

namespace php\db;
use PDO;
use Exception;
use \php\event\ConnectionEventArgs;
use \php\db\query\ExpressionBuilder;

/**
 * A wrapper around a Doctrine\DBAL\Driver\Connection that adds features like
 * events, transaction isolation levels, configuration, emulated transaction nesting,
 * lazy connecting and more.
 *
 */
class Connection implements ConnectionInterface {
    /**
     * Constant for transaction isolation level READ UNCOMMITTED.
     */
    const TRANSACTION_READ_UNCOMMITTED = 1;

    /**
     * Constant for transaction isolation level READ COMMITTED.
     */
    const TRANSACTION_READ_COMMITTED = 2;

    /**
     * Constant for transaction isolation level REPEATABLE READ.
     */
    const TRANSACTION_REPEATABLE_READ = 3;

    /**
     * Constant for transaction isolation level SERIALIZABLE.
     */
    const TRANSACTION_SERIALIZABLE = 4;

    /**
     * Represents an array of ints to be expanded by Doctrine SQL parsing.
     *
     * @var integer
     */
    const PARAM_INT_ARRAY = 101;

    /**
     * Represents an array of strings to be expanded by Doctrine SQL parsing.
     *
     * @var integer
     */
    const PARAM_STR_ARRAY = 102;

    /**
     * Offset by which PARAM_* constants are detected as arrays of the param type.
     *
     * @var integer
     */
    const ARRAY_PARAM_OFFSET = 100;

    /**
     * The wrapped driver connection.
     *
     * @var \php\db\ConnectionInterface
     */
    protected $_conn;

    /**
     * @var \php\db\Configuration
     */
    protected $_config;

    /**
     * @var \php\event\EventManager
     */
    protected $_eventManager;

    /**
     * @var \php\db\query\ExpressionBuilder
     */
    protected $_expr;

    /**
     * Whether or not a connection has been established.
     *
     * @var boolean
     */
    private $_isConnected = false;

    /**
     * The current auto-commit mode of this connection.
     *
     * @var boolean
     */
    private $autoCommit = true;

    /**
     * The transaction nesting level.
     *
     * @var integer
     */
    private $_transactionNestingLevel = 0;

    /**
     * The currently active transaction isolation level.
     *
     * @var integer
     */
    private $_transactionIsolationLevel;

    /**
     * If nested transactions should use savepoints.
     *
     * @var boolean
     */
    private $_nestTransactionsWithSavepoints = false;

    /**
     * The parameters used during creation of the Connection instance.
     *
     * @var array
     */
    private $_params = array();

    /**
     * The DatabasePlatform object that provides information about the
     * database platform used by the connection.
     *
     * @var \php\db\AbstractPlatform
     */
    private $platform;

    /**
     * The schema manager.
     *
     * @var \php\db\query\AbstractSchemaManager
     */
    protected $_schemaManager;

    /**
     * The used DBAL driver.
     *
     * @var \php\db\Driver
     */
    protected $_driver;

    /**
     * Flag that indicates whether the current transaction is marked for rollback only.
     *
     * @var boolean
     */
    private $_isRollbackOnly = false;

    /**
     * @var integer
     */
    protected $defaultFetchMode = PDO::FETCH_ASSOC;

    /**
     * Initializes a new instance of the Connection class.
     *
     * @param array                              $params       The connection parameters.
     * @param \php\db\Driver              $driver       The driver to use.
     * @param \php\db\Configuration|null  $config       The configuration, optional.
     * @param \php\event\EventManager|null $eventManager The event manager, optional.
     *
     * @throws \php\db\SQLException
     */
    public function __construct(array $params, Driver $driver, Configuration $config = null,
                                EventManager $eventManager = null) {
        $this->_driver = $driver;
        $this->_params = $params;

        if (isset($params['pdo'])) {
            $this->_conn = $params['pdo'];
            $this->_isConnected = true;
            unset($this->_params['pdo']);
        }

        // Create default config and event manager if none given
        if ( ! $config) {
            $config = new Configuration();
        }

        if ( ! $eventManager) {
            $eventManager = new EventManager();
        }

        $this->_config = $config;
        $this->_eventManager = $eventManager;

        $this->_expr = new ExpressionBuilder($this);

        $this->autoCommit = $config->getAutoCommit();
    }

    /**
     * Gets the parameters used during instantiation.
     *
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }

    /**
     * Gets the name of the database this Connection is connected to.
     *
     * @return string
     */
    public function getDatabase() {
        return $this->_driver->getDatabase($this);
    }

    /**
     * Gets the hostname of the currently connected database.
     *
     * @return string|null
     */
    public function getHost() {
        return isset($this->_params['host']) ? $this->_params['host'] : null;
    }

    /**
     * Gets the port of the currently connected database.
     *
     * @return mixed
     */
    public function getPort() {
        return isset($this->_params['port']) ? $this->_params['port'] : null;
    }

    /**
     * Gets the username used by this connection.
     *
     * @return string|null
     */
    public function getUsername() {
        return isset($this->_params['user']) ? $this->_params['user'] : null;
    }

    /**
     * Gets the password used by this connection.
     *
     * @return string|null
     */
    public function getPassword() {
        return isset($this->_params['password']) ? $this->_params['password'] : null;
    }

    /**
     * Gets the DBAL driver instance.
     *
     * @return \php\db\Driver
     */
    public function getDriver() {
        return $this->_driver;
    }

    /**
     * Gets the Configuration used by the Connection.
     *
     * @return \php\db\Configuration
     */
    public function getConfiguration() {
        return $this->_config;
    }

    /**
     * Gets the EventManager used by the Connection.
     *
     * @return \php\event\EventManager
     */
    public function getEventManager() {
        return $this->_eventManager;
    }

    /**
     * Gets the DatabasePlatform for the connection.
     *
     * @return \php\db\AbstractPlatform
     */
    public function getDatabasePlatform() {
        if (null == $this->platform) {
            $this->detectDatabasePlatform();
        }

        return $this->platform;
    }

    /**
     * Gets the ExpressionBuilder for the connection.
     *
     * @return \php\db\query\ExpressionBuilder
     */
    public function getExpressionBuilder() {
        return $this->_expr;
    }

    /**
     * Establishes the connection with the database.
     *
     * @return boolean TRUE if the connection was successfully established, FALSE if
     *                 the connection is already open.
     */
    public function connect() {
        if ($this->_isConnected) return false;

        $driverOptions = isset($this->_params['driverOptions']) ?
            $this->_params['driverOptions'] : array();
        $user = isset($this->_params['user']) ? $this->_params['user'] : null;
        $password = isset($this->_params['password']) ?
            $this->_params['password'] : null;

        $this->_conn = $this->_driver->connect($this->_params, $user, $password, $driverOptions);
        $this->_isConnected = true;

        if (null === $this->platform) {
            $this->detectDatabasePlatform();
        }

        if (false === $this->autoCommit) {
            $this->beginTransaction();
        }

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            $eventArgs = new ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }

        return true;
    }

    /**
     * Detects and sets the database platform.
     *
     * Evaluates custom platform class and version in order to set the correct platform.
     *
     * @throws DBException if an invalid platform was specified for this connection.
     */
    private function detectDatabasePlatform() {
        if ( ! isset($this->_params['platform'])) {
            $version = $this->getDatabasePlatformVersion();

            if (null !== $version) {
                $this->platform = $this->_driver->createDatabasePlatformForVersion($version);
            } else {
                $this->platform = $this->_driver->getDatabasePlatform();
            }
        } elseif ($this->_params['platform'] instanceof AbstractPlatform) {
            $this->platform = $this->_params['platform'];
        } else {
            throw DBException::invalidPlatformSpecified();
        }

        $this->platform->setEventManager($this->_eventManager);
    }

}