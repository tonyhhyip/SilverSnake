<?php
if (!defined("RESTfulRequest")) {
	define("RESTfulRequest", 1);

	require_once("Servlet/HttpServletRequest.php");
	require_once("Servlet/Cookie.php");
	require_once("Servlet/RESTfulSession.php");

	class RESTfulRequest implements HttpServletRequest {
		private $parameter = array();
		private $cookie = array();
		private $session = null;
		private $header = array();
		private $env = array();
		private $attr = array();

		/**
		* Create a new HttpSerlveetRequest.
		* 
		* @param param
		*			Form input.
		* @param server
		*			$_SERVER array in php.
		* @param cookie
		*			$_COOKIE array in php.
		* @param session
		* 			$_SESSION array in php.
		* @param segement
		*			RESTful input.
		*/
		public function __construct($param, $server, $cookie, $session, $segement) {

			// Load form input.
			$this->parameter = $param;

			// Joining PATH_INFO and Form together.
			while (count($segement) > 0) {
				$name = array_shift($segement);
				$value = array_shift($segement);
				$this->parameter[$name] = $value;
			}

			// Loading Cookie
			foreach ($cookie as $name => $value) {
				// TODO include other detaul.
				array_push($this->cookie, new Cookie($name, $value));
			}

			// Loading Session.
			if (isset($session)) {
				$session = new RESTfulSession($session);
			}

			// Split $_SERVER into $ENV and headers.
			foreach ($server as $name => $value) {
				$name = str_replace("_", "-", $name);
				$value = str_replace("\\", "/", $value);
				if (preg_match("/^HTTP-/", $name)) {
					$arg = explode("-", $name);
					foreach ($arg as $v) {
						$v = ucfirst(strtolower($v));
					}
					$header[implode("-", $arg)] = $value;
					unset($arg, $v);
				} else {
					$this->env[$name] = $value;
				}
			}
		}

		public function getAttribute($name) {
			return isset($this->attr[$name]) ? $this->attr[$name] : null;
		}
		
		public function getAttributeNames() {
			return array_keys($this->attr);
		}
		
		public function setAttribute($name, $o) {
			$this->attr[$name] = $o;
		}
		
		public function removeAttribute($name) {
			unset($this->env[$name]);
		}
		
		public function getContentLength() {
			return ob_get_length();
		}
		
		public function getContentType() {
			return $this->env["CONTENT_TYPE"];
		}
		
		public function getLocalAddr() {
			return $this->env["SERVER-ADDR"];
		}
		
		public function getLocalName() {
			return $this->env["SERVER-NAME"];
		}
		
		public function getLocalPort() {
			return $this->env["SERVER-PORT"];
		}
		
		public function getParameter($name) {
			return isset($this->parameter[$name])? $this->parameter[$name] : null;
		}
		
		public function getParameterMap() {
			return $this->parameter;
		}
		
		public function getParameterNames() {
			array_keys($this->parameter);
		}
		
		public function getParameterValues($name){
			return $this->getParameter($name);
		}
		
		public function getProtocol() {
			return $this->env["SERVER-PROTOCOL"];
		}
		
		public function getRemoteAddr() {
			return $this->env["REMOTE-ADDR"];
		}
		
		public function getRemoteHost() {
			if ($host = gethostbyaddr($this->getRemoteAddr())) {
				return $host;
			} else {
				return $this->getRemoteAddr();
			}
		}
		
		public function getScheme() {
			return $this->env["REQUEST-SCHEME"];
		}
		
		public function getServerName() {
			return $this->env["SERVER-NAME"];
		}
		
		public function getServerPort() {
			return $this->env["SERVER-PORT"];
		}
		
		public function changeSessionId() {
			session_regenerate_id();
		}
		
		public function getCookies() {
			return $this->cookies;
		}
		
		public function getDateHeader($name) {
			return strtotime($this->getHeader($name));
		}
		
		public function getHeader($name) {
			return is_array($this->header[$name]) ? $this->header[$name][0] : $this->header[$name];
		}
		
		public function getHeaderNames() {
			return array_keys($this->header);
		}
		
		public function getHeaders($name) {
			return $this->header[$name];
		}
		
		public function getIntHeader($name) {
			return intval($this->getHeader($name));
		}
		public function getMethod() {
			return $this->env["REQUEST-METHOD"];
		}
		public function getPathInfo() {
			return isset($this->env["PATH-INFO"]) ? $this->env["PATH-INFO"] : null;
		}
		public function getPathTranslated() {
			return isset($this->env["PATH-TRANSLATED"]) ? $this->env["PATH-TRANSLATED"] : $this->env["SCRIPT-FILENAME"];
		}
		public function getQueryString() {
			return isset($this->env["QUERY-STRING"]) ? $this->env["QUERY-STRING"] : null;
		}
		public function getRemoteUser() {
			return isset($this->env["REMOTE-USER"]) ? $this->env["REMOTE-USER"] : null;
		}
		
		public function getRequestURI() {
			return $this->env["REQUEST-URI"];
		}
		
		public function getRequestURL() {
			return $this->env["REQUEST-SCHEME"] . "://" . $this->getHeader("Host") . $this->env["SCRIPT-NAME"];
		}
		
		public function getServletPath() {
			return substr($this->env["SCRIPT-NAME"], 1);
		}
		
		public function getSession($create = null) {
			if ($create === true){
				session_start();
			}
			return $this->session;
		}
		
		public function getContextPath() {
			return $this->env["REQUEST-URI"];
		}
		
		public function getRemotePort() {
			return $this->env["REMOTE-ADDR"];
		}
		
	}
}
?>