<?php

/**
 * @package php\db\mysql
 * package php.db.mysql;
 * @file MySQLDbConnection
 */

namespace php\db\mysql;

/**
 * Database interface code for MySQL database servers.
 */
class MySQLDbConnection extends \php\db\DbConnection {
	/**
	 * Flag to indicate if the cleanup function in __destruct() should run.
	 * 
	 * @var boolean
	 */
	protected $isCleanUp = false;
	
	const DRIVER_NAME = 'mysql';
	
	protected static $defaultOptions = array(
				'transaction' => true,
				'host' => 'localhost',
				'port' => 3306,
				'pdo' => array(
						// So we don't have to mess around with cursors and unbuffered queries by default.
						\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
						// Because MySQL's prepared statements skip the query cache, because it's dumb.
						\PDO::ATTR_EMULATE_PREPARES => true
				),
				'collation' => 'utf8_unicode_ci',
				'initCommands' => array(
					'sqlMode' => "SET sql_mode = 'ANSI,STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER'"
				),
				'charset' => 'utf8'
			);
	
	/**
	 * Create a connection to MySQL.
	 * The detail of the connection(e.g.: username, host, port) should be in an key-to-value
	 * array and pass it as the parameter $options.
	 * Here is the list of keys and its detail information:
	 * 
	 * transaction - a boolean value of transcation supported by the database engine,
	 * 					defaults with TRUE.
	 * unixSocket - a string of UNIX Socket.
	 * 				unixSocket will be used if both unixSocket and host exists.
	 * host - a string of hostname or IP, defaults as 'localhost' or '127.0.0.1'.
	 * port - a integer number of connection port. It should be using with host, defaults as 3306.
	 * database - a string of the name of the database.
	 * pdo - an array of PDO Attributes, defaults as an array where
	 * 			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
	 * 			PDO::ATTR_EMULATE_PREPARES => TRUE
	 * username - the username to be used for login.
	 * password - the password of the username.
	 * collation - collation to be used, defaults as utf8_unicode_ci
	 * initCommands - The sql command to init the connection, defaults as an array where
	 * 					sqlMode => SET sql_mode = 'ANSI,STRICT_TRANS_TABLES,STRICT_ALL_TABLES,
	 * 									NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,
	 * 									NO_AUTO_CREATE_USER'
	 * charset - Charset to be used, defaults as utf8
	 * 
	 * @param $options Details of connection.
	 */
	public function __construct(array $options = array()) {
		$options += self::$defaultOptions;
		$options['pdo'] += self::$defaultOptions['pdo'];
		$options['initCommands'] += self::$defaultOptions['initCommands'];
		$dsn = "mysql:";
		if (isset($options['unixSocket'])) {
			$dsn .= 'unix_socket:' . $options['unixSocket'];
		} else {
			$dsn .= 'host:' . $options['host'] . ";port=" . intval($options['port']);
		}
		$dsn .= ";dbname=" . $options['database'];
		
		parent::__construct($dsn, $options['username'], $options['password'], $options['pdo']);
		
		$this->exec(sprintf('SET NAMES %s COLLATE %s', $options['charset'], $options['collation']));
		$this->exec(implode(';', $options['initCommands']));
	}
	
	/**
	 * Release memory and close the connection.
	 */
	public function __destruct() {
		if ($this->isCleanUp) {
			$this->nextIdDelete();
		}
	}
	
	/**
	 * Set the range of the query with add LIMIT into the query.
	 * 
	 * @param string $query The SQL Statement.
	 * @param int $start The start counting record.
	 * @param int $count number of record to be returned.
	 * @param array $args arguments of the SQL.
	 * @param array $options Options of the query.
	 */
	public function queryRange($query, $from, $count, array $args = array(), array $options = array()) {
		$this->query(sprintf("%s LIMIT %d, %d", $query, $start, $count), $args, $options);
	}
	
	public function queryTemporary($query, array $args = array(), array $options = array()) {
		$tablename = $this->generateTemporaryTableName();
		$this->query('CREATE TEMPORARY TABLE {' . $tablename . '} Engine=MEMORY ' . $query, $args, $options);
		return $tablename;
	}
	
	public function driver() {
		return self::DRIVER_NAME;
	}
	
	public function databaseType() {
		return self::DRIVER_NAME;
	}
	
	public function mapConditionOperator($operator) {
		return null;
	}
	
	public function nextId($existing_id = 0) {
		$new_id = $this->query('INSERT INTO {sequences} () VALUES ()', array(), array('return' => \php\db\Database::RETURN_INSERT_ID));
		// This should only happen after an import or similar event.
		if ($existing_id >= $new_id) {
			// If we INSERT a value manually into the sequences table, on the next
			// INSERT, MySQL will generate a larger value. However, there is no way
			// of knowing whether this value already exists in the table. MySQL
			// provides an INSERT IGNORE which would work, but that can mask problems
			// other than duplicate keys. Instead, we use INSERT ... ON DUPLICATE KEY
			// UPDATE in such a way that the UPDATE does not do anything. This way,
			// duplicate keys do not generate errors but everything else does.
			$this->query('INSERT INTO {sequences} (value) VALUES (:value) ON DUPLICATE KEY UPDATE value = value', array(':value' => $existing_id));
			$new_id = $this->query('INSERT INTO {sequences} () VALUES ()', array(), array('return' => \php\db\Database::RETURN_INSERT_ID));
		}
		$this->isCleanUp = true;
		return $new_id;
	}
	
	public function nextIdDelete() {
		// While we want to clean up the table to keep it up from occupying too
		// much storage and memory, we must keep the highest value in the table
		// because InnoDB  uses an in-memory auto-increment counter as long as the
		// server runs. When the server is stopped and restarted, InnoDB
		// reinitializes the counter for each table for the first INSERT to the
		// table based solely on values from the table so deleting all values would
		// be a problem in this case. Also, TRUNCATE resets the auto increment
		// counter.
		try {
			$max_id = $this->query('SELECT MAX(value) FROM {sequences}')->fetchField();
			// We know we are using MySQL here, no need for the slower db_delete().
			$this->query('DELETE FROM {sequences} WHERE value < :value', array(':value' => $max_id));
		} catch (PDOException $e) {
		// During testing, this function is called from shutdown with the
		// simpletest prefix stored in $this->connection, and those tables are gone
		// by the time shutdown is called so we need to ignore the database
		// errors. There is no problem with completely ignoring errors here: if
		// these queries fail, the sequence will work just fine, just use a bit
		// more database storage and memory.
		
		}
	}
}

?>