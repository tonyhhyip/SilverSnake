<?php

/**
 * @package \php\servlet
 * package php.servlet;
 * @file ServletContanier
 */


  
/**
 * Servlet Container. Use as a web container like Tomcat in Java.
 * @version 0.0.2
 */
class ServletContainer  {

	private $segment = null;
	private $control = null;
    
	public function __construct() {
		if (!isset($_SERVER["PATH_INFO"]) || $_SERVER['PATH_INFO'] == "/") {
			return ;
		}

		// Process PATH_INFO
		$this->segment = explode("/", $_SERVER['PATH_INFO']);
		// The first is empty and meaningless.
		array_shift($this->segment);
		// Servlet Class Name (Upper case for first letter)
		$controlName = ucfirst(array_shift($this->segment));
		
		// Load Class.
		try {
			$this->control = new $controlName;
		} catch (Exception $e) {
			self::sendError(404, "Not Found");
			die();
		}
	}

	public function run() {
		if ($this->control == null) {
			include "index.class.php";
			$this->control = new Index();
		}

		$parameter = $_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST" ? $_REQUEST : json_decode(file_get_contents("php://input"), 1);
		
		$request = new \php\servlet\RESTfulRequest($parameter, $_SERVER, $_COOKIE, isset($_SESSION)? $_SESSION : null, $this->segment);
		$response = new \php\servlet\RESTfulResponse($request);

		$this->control->service($request, $response);
		$response->send();
	}


	private static function exceptionResponse($statusCode, $message) {
		header($_SERVER["SERVER_PROTOCOL"] . " $statusCode $message");
		header("Status: $statusCode");
		exit();
	}
}
?>
