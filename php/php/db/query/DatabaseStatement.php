<?php

/**
 * @package php\db
 * package php.db;
 */

namespace php\db\query;

use php\db\DbConnection;
/**
 * Default implementation of DatabaseStatementInterface.
 * @author Tony Yip
 */
class DatabaseStatement extends \PDOStatement implements DatabaseStatementInterface {
	/**
	 * Reference to the database connection object for this statement.
	 *
	 * The name $dbh is inherited from PDOStatement.
	 *
	 * @var DbConnection
	 */
	public $dbh;
	
	protected function __construct(\PDO $dbh) {
		$this->dbh = $dbh;
		$this->setFetchMode(\PDO::FETCH_OBJ);
	}
	
public function execute($args = array(), $options = array()) {
    if (isset($options['fetch'])) {
      if (is_string($options['fetch'])) {
        $this->setFetchMode(PDO::FETCH_CLASS, $options['fetch']);
      }
      else {
        $this->setFetchMode($options['fetch']);
      }
    }

    $logger = $this->dbh->getLogger();
    if (!empty($logger)) {
      $query_start = microtime(TRUE);
    }

    $return = parent::execute($args);

    if (!empty($logger)) {
      $query_end = microtime(TRUE);
      $logger->log($this, $args, $query_end - $query_start);
    }

    return $return;
  }
	
	public function getQueryString() {
		return $this->queryString;
	}
	
	public function fetchCol($index = 0) {
		return $this->fetchAll(PDO::FETCH_COLUMN, $index);
	}
	
	public function fetchAllAssoc($key, $fetch = NULL) {
		$return = array();
		if (isset($fetch)) {
			if (is_string($fetch)) {
				$this->setFetchMode(PDO::FETCH_CLASS, $fetch);
			}
			else {
				$this->setFetchMode($fetch);
			}
		
		}
		
		foreach ($this as $record) {
			$recordKey = is_object($record) ? $record->key : $recoed[$key];
			$return[$recordKey] = $record;
		}
		
		return $return;
	}
	
	public function fetchAllKeyed($key_index = 0, $value_index = 1) {
		$return = array();
		$this->setFetchMode(PDO::FETCH_NUM);
		foreach ($this as $record) {
			$return[$record[$key_index]] = $record[$value_index];
		}
		return $return;
	}
	
	public function fetchField($index = 0) {
		// Call PDOStatement::fetchColumn to fetch the field.
		return $this->fetchColumn($index);
	}
	
	public function fetchAssoc() {
		// Call PDOStatement::fetch to fetch the row.
		return $this->fetch(PDO::FETCH_ASSOC);
	}
}

?>