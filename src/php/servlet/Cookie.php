<?php
if (!defined("Cookie")) {
	define("Cookie");
	/**
	* Creates a cookie, a small amount of information sent by a servlet to a Web browser, saved by the browser, and later sent back to the server.
	* A cookie's value can uniquely identify a client, so cookies are commonly used for session management.
	* A cookie has a name, a single value, and optional attributes such as a comment, path and domain qualifiers, a maximum age, and a version number.
	* Some Web browsers have bugs in how they handle the optional attributes, so use them sparingly to improve the interoperability of your servlets.
	* The servlet sends cookies to the browser by using the HttpServletResponse.addCookie(javax.servlet.http.Cookie) method,
	* which adds fields to HTTP response headers to send cookies to the browser, one at a time.
	* The browser is expected to support 20 cookies for each Web server, 300 cookies total, and may limit cookie size to 4 KB each.
	* The browser returns cookies to the servlet by adding fields to HTTP request headers.
	* Cookies can be retrieved from a request by using the HttpServletRequest.getCookies() method.
	* Several cookies might have the same name but different path attributes.
	* 
	* Cookies affect the caching of the Web pages that use them. HTTP 1.0 does not cache pages that use cookies created with this class.
	* This class does not support the cache control defined with HTTP 1.1.
	* 
	* This class supports both the Version 0 (by Netscape) and Version 1 (by RFC 2109) cookie specifications.
	* By default, cookies are created using Version 0 to ensure the best interoperability.
	*/
	class Cookie {

		private $name;
		private $value;
		private $comment = null;
		private $domain = $_SERVER["HTTP_HOST"];
		private $maxAge = -1;
		private $path = "/";
		private $secure = false;
		private $version = 0;
		private $httpOnly = false;

		/**
		* Constructs a cookie with the specified name and value.
		* The name must conform to RFC 2109.
		* However, vendors may provide a configuration option that allows cookie names conforming to the original Netscape Cookie Specification to be accepted.
		* The name of a cookie cannot be changed once the cookie has been created.
		* The value can be anything the server chooses to send. Its value is probably of interest only to the server.
		* The cookie's value can be changed after creation with the setValue method.
		* By default, cookies are created according to the Netscape cookie specification.
		* The version can be changed with the setVersion method.
		* 
		* @param name
		*			the name of cookie
		* @param value
		*			the value of the cookie
		* @see #setValue(java.lang.String)
		* @see #setVersion(int)
		*/
		public function __construct($name, $value) {
			if (!preg_match("/[,;\s\W]*/", $name) || preg_match(("/Comment|Discard|Domain|Expires|Max-Age|Path|Secure|Version/i"), $name)) {
				throw new Exception("cookie name is null or empty or contains any illegal characters");
			}
			$this->name = $name;
			$this->value = $value;
			$this->send();
		}

		/**
		* Specifies a comment that describes a cookie's purpose.
		* The comment is useful if the browser presents the cookie to the user.
		* Comments are not supported by Netscape Version 0 cookies.
		* 
		* @param purpose
		* 			a String specifying the comment to display to the user
		* @see #getComment()
		*/
		public function setComment($purpose) {
			$this->comment = $purpose;
			$this->send();
		}

		/**
		* Returns the comment describing the purpose of this cookie, or null if the cookie has no comment.
		* 
		* @return the comment of the cookie, or null if unspecified
		* @see #setComment(java.lang.String)
		*/
		public function getComment() {
			return $this->comment;
		}

		/**
		* Specifies the domain within which this cookie should be presented.
		* The form of the domain name is specified by RFC 2109.
		* A domain name begins with a dot (.foo.com) and means that the cookie
		* is visible to servers in a specified Domain Name System (DNS) zone (for example, www.foo.com, but not a.b.foo.com).
		* By default, cookies are only returned to the server that sent them.
		* 
		* @param domain
		*			the domain name within which this cookie is visible; form is according to RFC 2109
		* @see #getDomain()			
		*/
		public function setDomain($domain) {
			$this->domain = strtolower($domain);
			$this->send();
		}

		/**
		* Gets the domain name of this Cookie.
		* Domain names are formatted according to RFC 2109.
		* 
		* @return the domain name of this Cookie.
		* @see #setDomain(java.lang.String)
		*/
		public function getDomain() {
			return $this->domain;
		}

		/**
		* Sets the maximum age in seconds for this Cookie.
		* A positive value indicates that the cookie will expire after that many seconds have passed.
		* Note that the value is the maximum age when the cookie will expire, not the cookie's current age.
		* A negative value means that the cookie is not stored persistently and will be deleted when the Web browser exits.
		* A zero value causes the cookie to be deleted.
		* 
		* @param expiry
		*			an integer specifying the maximum age of the cookie in seconds; if negative, means the cookie is not stored; if zero, deletes the cookie
		* @see #getMaxAge()
		*/
		public function setMaxAge($expiry) {
			$this->maxAge = $expiry;
			$this->send();
		}

		/**
		* Gets the maximum age in seconds of this Cookie.
		* By default, -1 is returned, which indicates that the cookie will persist until browser shutdown.
		* 
		* @return an integer specifying the maximum age of the cookie in seconds; if negative, means the cookie persists until browser shutdown
		* @see #setMaxAge(int)
		*/
		public function getMaxAge() {
			return $this->maxAge;
		}

		/**
		* Specifies a path for the cookie to which the client should return the cookie.
		* The cookie is visible to all the pages in the directory you specify, and all the pages in that directory's subdirectories.
		* A cookie's path must include the servlet that set the cookie,
		* for example, /catalog, which makes the cookie visible to all directories on the server under /catalog.
		* Consult RFC 2109 (available on the Internet) for more information on setting path names for cookies.
		* 
		* @param uri
		*		a String specifying a path.
		* @see #getPath()
		*/
		public function setPath($uri) {
			$this->path = $uri;
			$this->send();
		}

		/**
		* Returns the path on the server to which the browser returns this cookie. The cookie is visible to all subpaths on the server.
		* 
		* @return a String specifying a path that contains a servlet name, for example, /catalog
		* @see #setPath(java.lang.String)
		*/
		public function getPath() {
			return $this->path;
		}

		/**
		* Indicates to the browser whether the cookie should only be sent using a secure protocol, such as HTTPS or SSL.
		* The default value is false.
		* 
		* @param flag
		* 			if true, sends the cookie from the browser to the server only when using a secure protocol; if false, sent on any protocol
		* @see #getSecure()
		*/
		public function setSecure($flag) {
			$this->secure = $flag;
			$this->send();
		}
		
		/**
		* Returns true if the browser is sending cookies only over a secure protocol, or false if the browser can send cookies using any protocol.
		* 
		* @return true if the browser uses a secure protocol, false otherwise
		* @see #setSecure(boolean)
		*/
		public function getSecure() {
			return $this->secure;
		}

		/**
		* Returns the name of the cookie. The name cannot be changed after creation.
		* 
		* @return the name of the cookie
		*/
		public function getName() {
			return $this->name;
		}

		/**
		* Assigns a new value to this Cookie.
		* If you use a binary value, you may want to use BASE64 encoding.
		* With Version 0 cookies, values should not contain white space, brackets, parentheses, equals signs,
		* commas, double quotes, slashes, question marks, at signs, colons, and semicolons.
		* Empty values may not behave the same way on all browsers.
		*
		* @param newValue
		*			the new value of the cookie
		* @see #getValue()
		*/
		public function setValue($newValue) {
			$this->value = $newValue;
			$this->send();
		}

		/**
		* Gets the current value of this Cookie.
		* 
		* @return the current value of this Cookie
		* @see #setValue(java.lang.String)
		*/
		public function getValue() {
			return $this->value;
		}

		/**
		* Returns the version of the protocol this cookie complies with. Version 1 complies with RFC 2109,
		* and version 0 complies with the original cookie specification drafted by Netscape.
		* Cookies provided by a browser use and identify the browser's cookie version.
		* 
		* @return 0 if the cookie complies with the original Netscape specification; 1 if the cookie complies with RFC 2109
		* @see #setVersion(int)
		*/
		public function getVersion() {
			return $this->version;
		}

		/**
		* Sets the version of the cookie protocol that this Cookie complies with.
		* Version 0 complies with the original Netscape cookie specification. Version 1 complies with RFC 2109.
		* Since RFC 2109 is still somewhat new, consider version 1 as experimental; do not use it yet on production sites.
		* 
		* @param v
		* 		0 if the cookie should comply with the original Netscape specification; 1 if the cookie should comply with RFC 2109
		* @see #getVersion()
		*/
		public function setVersion($v) {
			$this->version = $v;
			$this->send();
		}
		
		/**
		* Overrides the standard <code>java.lang.Object.clone</code> method to return a copy of this Cookie.
		*/
		public function __clone() {
			$newCookie = new Cookie($this->name, $this->value);
			$newCookie->setVersion($this->version);
			$newCookie->setSecure($this->secure);
			$newCookie->setPath($this->path);
			$newCookie->setMaxAge($this->maxAge);
			$newCookie->setDomain($this->domain);
			return $newCookie;
		}

		/**
		* Marks or unmarks this Cookie as HttpOnly.
		* If isHttpOnly is set to true, this cookie is marked as HttpOnly, by adding the HttpOnly attribute to it.
		* HttpOnly cookies are not supposed to be exposed to client-side scripting code,
		* and may therefore help mitigate certain kinds of cross-site scripting attacks.
		* 
		* @param isHttpOnly
		*			true if this cookie is to be marked as HttpOnly, false otherwise
		* @since v0.0.3-beta
		*/
		public function setHttpOnly($isHttpOnly) {
			$this->httpOnly = $isHttpOnly;
			$this->send();
		}

		/**
		* Checks whether this Cookie has been marked as HttpOnly.
		* 
		* @return true if this Cookie has been marked as HttpOnly, false otherwise
		* @since v0.0.3-beta
		*/
		public function isHttpOnly() {
			return $this->httpOnly;
		}

		/**
		* Impleament all the update.
		*/
		protected function send() {
			setcookie($this->name, $this->value, $this->maxAge, $this->path, $this->domain, $this->secure, $this->httpOnly);
		}
	}
}
?>