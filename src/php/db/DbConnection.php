<?php
/**
 * @package \php\db
 * package php.db;
 */

namespace \php\db;

/**
 * Base Database API class.
 *
 * This class provides a extension of the PDO database abstraction class in PHP.
 * Every database driver implementation must provide a concrete implementation
 * of it to support special handling required by that database.
 *
 * @see http://php.net/manual/book.pdo.php
 */
abstract class DbConnection extends PDO {
	/**
	 * The database target this connection is for.
	 *
	 * We need this information for later auditing and logging.
	 *
	 * @var string
	 */
	protected $target = null;
	
	/**
	 * The key representing this connection.
	 *
	 * The key is a unique string which identifies a database connection. A
	 * connection can be a single server or a cluster of master and slaves (use
	 * target to pick between master and slave).
	 *
	 * @var string
	 */
	protected $key = null;
	
	/**
	 * The current database logging object for this connection.
	 *
	 * @var DatabaseLog
	 */
	protected $logger = null;
	
	/**
	 * The current database logging object for this connection.
	 *
	 * @var DatabaseLog
	 */
	protected $logger = NULL;
	
	/**
	 * Tracks the number of "layers" of transactions currently active.
	 *
	 * On many databases transactions cannot nest.  Instead, we track
	 * nested calls to transactions and collapse them into a single
	 * transaction.
	 *
	 * @var array
	 */
	protected $transactionLayers = array();
	
	/**
	 * Index of what driver-specific class to use for various operations.
	 *
	 * @var array
	*/
	protected $driverClasses = array();
	
	/**
	 * The name of the Statement class for this connection.
	 *
	 * @var string
	*/
	protected $statementClass = 'DatabaseStatementBase';
	
	/**
	 * Whether this database connection supports transactions.
	 *
	 * @var bool
	 */
	protected $transactionSupport = TRUE;
	
	/**
	 * Whether this database connection supports transactional DDL.
	 *
	 * Set to FALSE by default because few databases support this feature.
	 *
	 * @var bool
	 */
	protected $transactionalDDLSupport = FALSE;
	
	/**
	 * An index used to generate unique temporary table names.
	 *
	 * @var integer
	 */
	protected $temporaryNameIndex = 0;
	
	/**
	 * The connection information for this connection object.
	 *
	 * @var array
	 */
	protected $connectionOptions = array();
	
	/**
	 * The schema object for this connection.
	 *
	 * @var object
	*/
	protected $schema = NULL;
	
	/**
	 * The prefixes used by this database connection.
	 *
	 * @var array
	 */
	protected $prefixes = array();
	
	/**
	 * List of search values for use in prefixTables().
	 *
	 * @var array
	*/
	protected $prefixSearch = array();
	
	/**
	 * List of replacement values for use in prefixTables().
	 *
	 * @var array
	*/
	protected $prefixReplace = array();
	
	//@Override
	public function __construct($dsn, $username, $passwd, $options = array()) {
		// Initialize and prepare the connection prefix.
		$this->setPrefix(isset($this->connectionOptions['prefix']) ? $this->connectionOptions['prefix'] : '');
		
		// Because the other methods don't seem to work right.
		$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		
		parent::__construct($dsn, $username, $passwd, $options);
		
		if (!empty($this->statementClass)) {
			$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array($this->statementClass, array($this)));
		}
	}
	
	/**
	 * Destroys this Connection object.
	 *
	 * PHP does not destruct an object if it is still referenced in other
	 * variables. In case of PDO database connection objects, PHP only closes the
	 * connection when the PDO object is destructed, so any references to this
	 * object may cause the number of maximum allowed connections to be exceeded.
	 */
	public function destory() {
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('PDOStatement', array()));
		$this->schema = NULL;
	}
	
	/**
	 * Returns the default query options for any given query.
	 * 
	 * @return
	 *   An array of default query options.
	 */
	protected function defaultOptions() {
		return array(
			'target' => 'default',
			'fetch' => PDO::FETCH_ASSOC,
			'return' => Database::RETURN_STATEMENT,
			'throw_exception' => TRUE,
		);
	}
	
	/**
	 * Returns the connection information for this connection object.
	 *
	 * Note that Database::getConnectionInfo() is for requesting information
	 * about an arbitrary database connection that is defined. This method
	 * is for requesting the connection information of this specific
	 * open connection object.
	 *
	 * @return
	 *   An array of the connection information. The exact list of
	 *   properties is driver-dependent.
	 */
	public function getConnectionOptions() {
		return $this->connectionOptions;
	}
	
	/**
	 * Set the list of prefixes used by this database connection.
	 *
	 * @param $prefix
	 *   The prefixes, in any of the multiple forms documented in
	 *   default.settings.php.
	 */
	protected function setPrefix($prefix) {
		
	}
}