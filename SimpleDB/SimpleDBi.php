<?php

require_once(dirname(__FILE__) . "/SimpleDB.php");

interface SimpleDBi extends SimpleDB {
	public function startTransaction();
	public function rollBack();
	public function commit();
	public function isAutoCommit();
}

?>