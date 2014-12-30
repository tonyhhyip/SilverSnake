<?php
if (!defined("RESTfulResponse")) {
	define("RESTfulResponse", 1);
	
	require_once("Servlet/HttpServletResponse.php");
	
	/**
	 * Implements of HttpServeltResponse
	 * @author Tony Yip
	 */
	class RESTfulResponse implements HttpServletResponse {
		private $header = array();
		private $contentType = "text/html";
		private $charset;
		private $status;
		private $cookie = array();
		private $req;
		
		public function __construct(HttpServletRequest $req) {
			$tmp_header = headers_list();
			foreach ($tmp_header as $header){
				$tmp = explode(":", $header, 2);
				$this->headers[trim($tmp[0])] = trim($tmp[1]);
			}
			$this->charset = mb_internal_encoding();
			$this->status = 200;
			$this->req = $req;
		}
		
		public function flushBuffer() {
			ob_flush();
		}
		
		public function getBufferSize() {
			return ob_get_length();
		}
		
		public function getCharacterEncoding() {
			return $this->charset;
		}
		
		public function getContentType() {
			return $this->contentType;
		}
		
		public function setCharacterEncoding(String $charset) {
			$this->charset = $charset;
		}
		
		public function setContentLength(int $len) {
			$this->headers["Content-Lenth"] = $len;
		}
		
		public function setContentType($type) {
			$this->contentType = $type;
		}
		
		public function addCookie(Cookie $cookie) {
			$this->cookie[] = $cookie;
		}
		
		public function addDateHeader(String $name, int $date) {
			$this->setDateHeader($name, $date);
		}
		
		public function addHeader(String $name, $value) {
			$this->setHeader($name, $value);
		}
		
		public function addIntHeader(String $name, int $value) {
			$this->setHeader($name, intval($value));
		}
		
		public function containsHeader(String $name) {
			return isset($this->headers[$name]);
		}
		
		public function encodeRedirectURL(String $url) {
			$url = htmlspecialchars($url);
			if (!ini_get("session")) {
				$url .= ":" . session_id();
			}
			return $url;
		}
		
		public function encodeURL(String $url) {
			return htmlspecialchars($url);
		}
		
		public function getHeader(String $name) {
			return $this->headers[$name];
		}
		
		public function getHeaderNames() {
			$names = array();
			foreach ($this->headers as $name => $value) {
				$names[] = $name;
			}
			return $names;
		}
		
		public function getHeaders(String $name) {
			return $this->getHeader($name);
		}
		
		public function getStatus() {
			return $this->status;
		}
		
		public function sendError(int $sc, String $msg=null) {
			$this->setStatus($sc, $msg);
		}
		
		public function sendRedirect(String $location) {
			$this->setHeader("Location", $location);
		}
		
		public function setDateHeader(String $name, int $date) {
			$this->setHeader($name, gmdate("D, d M Y H:i:s", $date) . "GMT");
		}
		
		public function setHeader(String $name, $value) {
			$this->headers[$name] = $value;
		}
		
		public function setIntHeader(String $name, int $value) {
			$this->setHeader($name, intval($value));
		}
		
		public function setStatus($sc, $msg=null){
			if (!is_null($msg)) {
				header($this->req->getProtocol() . " " . $sc . " " . $msg);
			}
			$this->status = $sc;
		}
		
		/**
		 * Send the HttpServletResponse.
		 */
		public function send() {
			$this->setStatus($this->status);
			foreach ($this->headers as $name => $value) {
				header($name . ": " . $value);
			}
			ob_flush();
		}
	}
} 
?>