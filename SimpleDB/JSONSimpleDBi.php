<?php

require_once(dirname(__FILE__) . "/SimpleDB.php");
require_once(dirname(__FILE__) . "/SimpleDBi.php");
require_once(dirname(__FILE__) . "/JSONSimpleDB.php");

class JSONSimpleDBi extends JSONSimpleDB implements SimpleDBi {

	private $autoCommit = true;
	
	public function __construct($dbName) {
		parent::__construct($dbName);
	}

	public function startTransaction() {
		$this->autoCommit = false;
	}

	public function getAutoCommit() {
		return $this->autoCommit;
	}

	public function commit() {
		$this->autoCommit = true;
		$this->save();
	}

	public function rollback() {
		$this->autoCommit = true;
		$this->load();
	}

	protected function save() {
		if (!$this->getAutoCommit()) {
			parent::save();
		}
	}

	protected function load() {
		if (!$this->getAutoCommit()) {
			parent::load();
		}
	}
}

?>