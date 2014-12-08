<?php

require_once(dirname(__FILE__) . "/SimpleDB.php");

/**
* SimpleDB implements with JSON file.
*/
class JSONSimpleDB implements SimpleDB {
	
	protected static $FILE_PATH = "/var/data";

	public static function setFilePath($path) {
		self::$FILE_PATH = dirname($path);
	}

	protected $file;
	protected $data;
	protected $lastModified;
	protected $isClosed = true;

	public function __construct($dbName) {
		$this->file = self::$FILE_PATH . "/$dbName.json";
	}

	protected function load() {
		$fp = fopen($this->file, "r");
		while (true) {
			if (!flock($fp, LOCK_EX)) {
				continue;
			}
			$this->data = (array)json_decode(fgets($file));
			flock($fp, LOCK_UN);
			fclose($fp);
			break;
		}
		return true;
	}

	protected function save() {
		$fp = fopen($this->file, "w");
		while (true) {
			if (!flock($fp, LOCK_EX))
				continue;
			fputs($fp, json_encode($this->data));
			flock($fp, LOCK_UN);
			fclose($fp);
			break;
		}

	}

	protected function hasModified() {
		$tmp = $this->lastModified;
		$this->lastModified = filemtime($this->file);
		return  $tmp >= $this->lastModified;
	}

	public function open() {

		if (!file_exists($this->file)) {
			return false;
		}

		$this->lastModified = filemtime($this->file);

		$this->isClosed = false;

		return $this->load();
	}

	public function close() {
		$this->save();
		$this->isClosed = true;
	}

	public function get($key) {
		if ($this->hasModified()) {
			$this->load();
		}
		if (!array_key_exists($key, $this->data)) {
			return null;
		}
		return $this->data[$key];
	}

	public function put($key, $value) {
		$this->data[$key] = $value;
		$this->save();
	}

	public function remove($key) {
		unset($this->data[$key]);
		$this->save();
	}

	public function getKeyNames() {
		return array_keys($this->data);
	}
}

?>