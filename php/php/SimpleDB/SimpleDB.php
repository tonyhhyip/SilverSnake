<?php

interface SimpleDB {
	public function get($key);
	public function put($key, $value);
	public function open();
	public function close();
	public function remove($key);
	public function getKeyNames();
}

?>