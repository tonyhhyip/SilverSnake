<?php

require_once(dirname(__FILE__) . "/SimpleDB/JSONSimpleDBi.php");

class Session {
	private $map;
	private $db;
	private $id;

	public function __construct($id = null) {
		$this->id = $id == null ? session_id() : $id;
		$this->db = new JSONSimpleDBi("session");
		$this->db->open();
		$this->map = $this->db->get($this->id);
		if ($this->map == null) {
			$this->map = array();
		}
	}

	public function set($key, $value) {
		$this->map[$key] = $value;
		$this->db->put($this->id, $this->map);
	}

	public function destory() {
		$this->db->remove($this->id);
	}

	public function get($key) {
		return isset($this->map[$key]) ? $this->map[$key] : null;
	}

	public function getMap() {
		return $this->map;
	}

	public function setMap($map) {
		$this->map = $map;
		$this->db->put($this->id, $map);
	}
}

?>