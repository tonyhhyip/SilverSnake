<?php
if (!defined("HttpServletResponse")) {
	define("HttpServletResponse", 1);
	
	interface HttpServletResponse {
		
		/**
		 * Status code (100) indicating the client can continue.
		 */
		const SC_CONTINUE = 100;
		
		/**
		 * Status code (101) indicating the server 
		 * is switching protocols according to Upgrade header.
		 */
		const SC_SWITCHING_PROTOCOLS = 101;
		
		/**
		 * Status code (200) indicating the request succeeded normally.
		 */
		const SC_OK = 200;
		
		/**
		 * Status code (201) indicating the request succeeded 
		 * and created a new resource on the server.
		 */
		const SC_CREATED = 201;
		
		/**
		 * Status code (202) indicating that a request was accepted for processing,
		 * but was not completed.
		 */
		const SC_ACCEPTED = 202;
		
		/**
		 * Status code (203) indicating that the meta information presented
		 * by the client did not originate from the server.
		 */
		const SC_NON_AUTHORITATIVE_INFORMATION = 203;
		
		/**
		 * Status code (204) indicating that the request succeeded 
		 * but that there was no new information to return.
		 */
		const SC_NO_CONTENT = 204;
		
		/**
		* Status code (205) indicating that the agent SHOULD reset the document view which caused the request to be sent.
		*/
		const SC_RESET_CONTENT = 205;

		/**
		* Status code (206) indicating that the server has fulfilled the partial GET request for the resource.
		*/
		const SC_PARTIAL_CONTENT = 206;

		/**
		* Status code (300) indicating that the requested resource corresponds to any one of a set of representations,
		* each with its own specific location.
		*/
		const SC_MULTIPLE_CHOICES = 300;

		/**
		* Status code (301) indicating that the resource has permanently moved to a new location,
		* and that future references should use a new URI with their requests.
		*/
		const SC_MOVED_PERMANENTLY = 301;

		/**
		* Status code (302) indicating that the resource has temporarily moved to another location,
		* but that future references should still use the original URI to access the resource.
		* This definition is being retained for backwards compatibility. SC_FOUND is now the preferred definition.
		*/
		const SC_MOVED_TEMPORARILY = 302;

		/**
		* Status code (302) indicating that the resource reside temporarily under a different URI.
		* Since the redirection might be altered on occasion, the client should continue to use the Request-URI for future requests.(HTTP/1.1)
		* To represent the status code (302), it is recommended to use this variable.
		*/
		const SC_FOUND = 302;

		/**
		* Status code (303) indicating that the response to the request can be found
		* under a different URI.
		*/
		const SC_SEE_OTHER = 303;

		/**
		* Status code (304) indicating that a conditional GET operation found that
		* the resource was available and not modified.
		*/
		const SC_NOT_MODIFIED = 304;

		/**
		* Status code (305) indicating that the requested resource MUST be accessed
		* through the proxy given by the Location field.
		*/
		const SC_USE_PROXY = 305;

		/**
		* Status code (307) indicating that the requested resource resides temporarily
		* under a different URI.
		* The temporary URI SHOULD be given by the Location field in the response.
		*/
		const SC_TEMPORARY_REDIRECT = 307;

		/**
		* Status code (400) indicating the request sent by the client was syntactically incorrect.
		*/
		const SC_BAD_REQUEST = 400;

		/**
		* Status code (401) indicating that the request requires HTTP authentication.
		*/
		const SC_UNAUTHORIZED = 401;

		/**
		* Status code (402) reserved for future use.
		*/
		const SC_PAYMENT_REQUIRED = 402;

		/**
		* Status code (403) indicating the server understood the request but refused to fulfill it.
		*/
		const SC_FORBIDDEN = 403;

		/**
		* Status code (404) indicating that the requested resource is not available.
		*/
		const SC_NOT_FOUND = 404;

		/**
		* Status code (405) indicating that the method specified in the Request-Line
		* is not allowed for the resource identified by the Request-URI.
		*/
		const SC_METHOD_NOT_ALLOWED = 405;

		/**
		* Status code (406) indicating that the resource identified by the request is only
		* capable of generating response entities which have content characteristics not acceptable
		* according to the accept headers sent in the request.
		*/
		const SC_NOT_ACCEPTABLE = 406;

		/**
		* Status code (407) indicating that the client MUST first authenticate itself with the proxy.
		*/
		const SC_PROXY_AUTHENTICATION_REQUIRED = 407;

		/**
		* Status code (408) indicating that the client did not produce a request
		* within the time that the server was prepared to wait.
		*/
		const SC_REQUEST_TIMEOUT = 408;

		/**
		* Status code (409) indicating that the request could not be completed
		* due to a conflict with the current state of the resource.
		*/
		const SC_CONFLICT = 409;

		/**
		* Status code (410) indicating that the resource is no longer available
		* at the server and no forwarding address is known.
		* This condition SHOULD be considered permanent.
		*/
		const SC_GONE = 410;

		/**
		* Status code (411) indicating that the request cannot be handled
		* without a defined Content-Length.
		*/
		const SC_LENGTH_REQUIRED = 411;

		/**
		* Status code (412) indicating that the precondition given
		* in one or more of the request-header fields evaluated to false
		* when it was tested on the server.
		*/
		const SC_PRECONDITION_FAILED = 412;

		/**
		* Status code (413) indicating that the server is refusing to process the request
		* because the request entity is larger than the server is willing or able to process.
		*/
		const SC_REQUEST_ENTITY_TOO_LARGE = 413;

		/**
		* Status code (414) indicating that the server is refusing to service the request
		* because the Request-URI is longer than the server is willing to interpret.
		*/
		const SC_REQUEST_URI_TOO_LONG = 414;

		/**
		* Status code (415) indicating that the server is refusing to service the request
		* because the entity of the request is in a format not supported
		* by the requested resource for the requested method.
		*/
		const SC_UNSUPPORTED_MEDIA_TYPE = 415;

		/**
		* Status code (416) indicating that the server cannot serve the requested byte range.
		*/
		const SC_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

		/**
		* Status code (417) indicating that the server could not meet the expectation given in
		* the Expect request header.
		*/
		const SC_EXPECTATION_FAILED = 417;

		/**
		* Status code (500) indicating an error inside the HTTP server which prevented it
		* from fulfilling the request.
		*/
		const SC_INTERNAL_SERVER_ERROR = 500;

		/**
		* Status code (501) indicating the HTTP server does not support the functionality needed to
		* fulfill the request.
		*/
		const SC_NOT_IMPLEMENTED = 501;

		/**
		* Status code (502) indicating that the HTTP server received an invalid response
		* from a server it consulted when acting as a proxy or gateway.
		*/
		const SC_BAD_GATEWAY = 502;

		/**
		* Status code (503) indicating that the HTTP server is temporarily overloaded,
		* and unable to handle the request.
		*/
		const SC_SERVICE_UNAVAILABLE = 503;

		/**
		* Status code (504) indicating that the server did not receive a timely response
		* from the upstream server while acting as a gateway or proxy.
		*/
		const SC_GATEWAY_TIMEOUT = 504;

		/**
		* Status code (505) indicating that the server does not support
		* or refuses to support the HTTP protocol version that was used in the request message.
		*/
		const SC_HTTP_VERSION_NOT_SUPPORTED = 505;
		
		/**
		 * Forces any content in the buffer to be written to the client.
		 * A call to this method automatically commits the response,
		 * meaning the status code and headers will be written.
		 * 
		 * @see #getBufferSize()
		 */
		public function flushBuffer();
		
		/**
		 * Returns the actual buffer size used for the response.
		 * If no buffering is used, this method returns 0.
		 * 
		 * @return the actual buffer size used
		 * @see flushBuffer()
		 */
		public function getBufferSize();
		
		/**
		 * Returns the name of the character encoding (MIME charset) used for
		 * the body sent in this response.
		 * The character encoding may have been specified explicitly using the
		 * setCharacterEncoding(java.lang.String) or setContentType(java.lang.String) methods,
		 * Explicit specifications take precedence over implicit specifications.
		 * If no character encoding has been specified, ISO-8859-1 is returned.
		 * See RFC 2047 (http://www.ietf.org/rfc/rfc2047.txt) 
		 * for more information about character encoding and MIME.
		 * 
		 * @return a String specifying the name of the character encoding, for example, UTF-8
		 */
		public function getCharacterEncoding();
		
		/**
		 * Returns the content type used for the MIME body sent in this response.
		 * The content type proper must have been specified
		 * using setContentType(java.lang.String) before the response is committed.
		 * If no content type has been specified, this method returns null.
		 * If a content type has been specified, and a character encoding
		 * has been explicitly or implicitly specified as described
		 * in getCharacterEncoding() or getWriter() has been called,
		 * the charset parameter is included in the string returned.
		 * If no character encoding has been specified, the charset parameter is omitted.
		 * 
		 * @return a String specifying the content type,
		 * 				for example, text/html; charset=UTF-8, or null
		 */
		public function getContentType();
		
		/**
		 * Sets the character encoding (MIME charset) of the response being sent to the client,
		 * for example, to UTF-8.
		 * If the character encoding has already been set by setContentType(java.lang.String),
		 * this method overrides it.
		 * Calling setContentType(java.lang.String) with the String of text/html
		 * and calling this method with the String of UTF-8 is equivalent
		 * with calling setContentType with the String of text/html; charset=UTF-8.
		 * This method can be called repeatedly to change the character encoding.
		 * In the case of HTTP, the character encoding is communicated as part of
		 * the Content-Type header for text media types.
		 * Note that the character encoding cannot be communicated via HTTP headers
		 * if the servlet does not specify a content type;
		 * 
		 * @param charset
		 * 			a String specifying only the character set defined by
		 * 			IANA Character Sets (http://www.iana.org/assignments/character-sets)
		 * @see #setContentType(java.lang.String)
		 */
		public function setCharacterEncoding($charset);
		
		/**
		 * Sets the length of the content body in the response In HTTP servlets,
		 * this method sets the HTTP Content-Length header.
		 * 
		 * @param len
		 * 			an integer specifying the length of the content being returned to the client;
		 * 			sets the Content-Length header
		 */
		public function setContentLength($len);
		
		/**
		 * Sets the content type of the response being sent to the client,
		 * if the response has not been committed yet.
		 * The given content type may include a character encoding specification,
		 * for example, text/html;charset=UTF-8.
		 * This method may be called repeatedly to change content type and character encoding.
		 * This method has no effect if called after the response has been committed.
		 * 
		 * 
		 * @param type
		 * 			a String specifying the MIME type of the content.
		 * @see #setCharacterEncoding(java.lang.String)
		 */
		public function setContentType($type);
		
		
		/**
		 * Adds the specified cookie to the response.
		 * This method can be called multiple times to set more than one cookie.
		 * 
		 * @param Cookie cookie
		 * 			the Cookie to return to the client
		 */
		public function addCookie($cookie);

		/**
		* Adds a response header with the given name and date-value.
		* The date is specified in terms of milliseconds since the epoch.
		* This method allows response headers to have multiple values.
		* 
		* @param name
		*			the name of the header to set.
		* @param date
		*			the additional date value
		* @see #setDateHeader(java.lang.String, long)
		*/
		public function addDateHeader($name, $date);
		
		/**
		 * Adds a response header with the given name and value.
		 * This method allows response headers to have multiple values.
		 * @param name The name of the header
		 * @param value 
		 * 			The additional header value
		 * 			If it contains octet string,
		 * 			it should be encoded according to RFC 2047
		 * 			(http://www.ietf.org/rfc/rfc2047.txt)
		 * @see #setHeader(String, String)
		 */
		public function addHeader($name, $value);
		
		/**
		 * Adds a response header with the given name and integer value.
		 * This method allows response headers to have multiple values.
		 * 
		 * @param name
		 * 			The name of the header
		 * @param value
		 * 			The assigned integer value
		 * @see #setIntHeader(java.lang.String, int)
		 */
		public function addIntHeader($name, $value);
		
		/**
		 * Returns a boolean indicating whether the named response header has already been set.
		 * 
		 * @param name The header name
		 * @return true if the named response header has already been set; false otherwise
		 */
		public function containsHeader($name);
		
		/**
		 * Encodes the specified URL for use in the sendRedirect method or,
		 * if encoding is not needed, returns the URL unchanged.
		 * The implementation of this method includes the logic
		 * to determine whether the session ID needs to be encoded in the URL.
		 * For example, if the browser supports cookies, or session tracking is turned off,
		 * URL encoding is unnecessary.
		 * Because the rules for making this determination can differ from those used to decide whether to encode a normal link
		 * this method is separated from the encodeURL method.
		 * All URLs sent to the HttpServletResponse.sendRedirect method should be run through this method.
		 * Otherwise, URL rewriting cannot be used with browsers which do not support cookies.
		 * If the URL is relative, it is always relative to the current HttpServletRequest.
		 * 
		 * @param url the url to be encoded.
		 * @return the encoded URL if encoding is needed; the unchanged URL otherwise.
		 * @see #sendRedirect(java.lang.String)
		 * @see #encodeUrl(java.lang.String)
		 */
		public function encodeRedirectURL($url);
		
		/**
		 * Encodes the specified URL by including the session ID,
		 * or, if encoding is not needed, returns the URL unchanged.
		 * The implementation of this method includes the logic to determine
		 * whether the session ID needs to be encoded in the URL.
		 * For example, if the browser supports cookies, 
		 * or session tracking is turned off, URL encoding is unnecessary.
		 * For robust session tracking, all URLs emitted by a servlet should be run through this method.
		 * Otherwise, URL rewriting cannot be used with browsers which do not support cookies.
		 * If the URL is relative, it is always relative to the current HttpServletRequest.
		 * 
		 * @param url The url to be encoded.
		 * @return The encoded URL if encoding is needed; the unchanged URL otherwise.
		 */
		public function encodeURL($url);
		
		/**
		 * Gets the value of the response header with the given name.
		 * If a response header with the given name exists and contains multiple values,
		 * the value that was added first will be returned.
		 * This method considers only response headers set or added via
		 * setHeader(java.lang.String, java.lang.String),
		 * addHeader(java.lang.String, java.lang.String), 
		 * setDateHeader(java.lang.String, long), 
		 * addDateHeader(java.lang.String, long), 
		 * setIntHeader(java.lang.String, int), 
		 * or addIntHeader(java.lang.String, int), respectively.
		 * 
		 * @param name The name of the response header whose value to return.
		 * @return The value of the response header with the given name,
		 * 			or null if no header with the given name has been set on this response
		 */
		public function getHeader($name);
		
		/**
		 * Gets the names of the headers of this response.
		 * This method considers only response headers set or added
		 * via setHeader(java.lang.String, java.lang.String),
		 * addHeader(java.lang.String, java.lang.String), 
		 * setDateHeader(java.lang.String, long), 
		 * addDateHeader(java.lang.String, long), 
		 * setIntHeader(java.lang.String, int), 
		 * or addIntHeader(java.lang.String, int), respectively.
		 * Any changes to the returned array must not affect this HttpServletResponse.
		 * 
		 * @return a (possibly empty) array of the names of the headers of this response
		 */
		public function getHeaderNames();
		
		/**
		 * Gets the values of the response header with the given name.
		 * This method considers only response headers set or added via
		 * setHeader(java.lang.String, java.lang.String), 
		 * addHeader(java.lang.String, java.lang.String), 
		 * setDateHeader(java.lang.String, long), 
		 * addDateHeader(java.lang.String, long), 
		 * setIntHeader(java.lang.String, int), 
		 * or addIntHeader(java.lang.String, int), respectively.
		 * Any changes to the returned Collection must not affect this HttpServletResponse.
		 * 
		 * @param name The name of the response header whose values to return.
		 * @return a (possibly empty) array of the values of the response header with the given name
		 * 
		 */
		public function getHeaders($name);
		
		/**
		 * Gets the current status code of this response.
		 * 
		 * @return The current status code of this response.
		 */
		public function getStatus();
		
		/**
		 * Sends an error response to the client using the specified status code
		 * and clears the buffer.
		 * The server will preserve cookies and may clear
		 * or update any headers needed to serve the error page as a valid response.
		 * If an error-page declaration has been made for
		 * the web application corresponding to the status code passed in,
		 * it will be served back the error page.
		 * After using this method,
		 * the response should be considered to be committed and should not be written to.
		 * 
		 * @param sc The error status code
		 * @param msg The status message
		 */
		public function sendError(int $sc, $msg);
		
		/**
		 * Sends a temporary redirect response to the client
		 * using the specified redirect location URL and clears the buffer.
		 * The buffer will be replaced with the data set by this method.
		 * Calling this method sets the status code to SC_FOUND 302 (Found).
		 * This method can accept relative URLs;
		 * the servlet container must convert the relative URL
		 * to an absolute URL before sending the response to the client.
		 * If the location is relative without a leading '/' the container interprets it
		 * as relative to the current request URI.
		 * If the location is relative with a leading '/' the container interprets it
		 * as relative to the servlet container root.
		 * If the location is relative with two leading '/' the container interprets it
		 * as a network-path reference
		 * (see RFC 3986: Uniform Resource Identifier (URI): Generic Syntax,
		 *  section 4.2 "Relative Reference").
		 * After using this method,
		 * the response should be considered to be committed and should not be written to. 
		 * 
		 * @param location The redirect location URL
		 */
		public function sendRedirect($location);
		
		/**
		 * Sets a response header with the given name and date-value.
		 * The date is specified in terms of milliseconds since the epoch.
		 * If the header had already been set, the new value overwrites the previous one.
		 * The containsHeader method can be used to test for the presence of a header before setting its value.
		 * @param name
		 * 			The name of the header to set.
		 * @param date
		 * 			The assigned date value.
		 * @see #containsHeader(java.lang.String)
		 * @see #addDateHeader(java.lang.String, long)
		 */
		public function setDateHeader($name, $date);
		
		/**
		 * Sets a response header with the given name and value.
		 * If the header had already been set, the new value overwrites the previous one.
		 * The containsHeader method can be used to test for the presence of a header before setting its value.
		 * 
		 * @param name
		 * 			The name of the header
		 * @param value
		 * 			The header value If it contains octet string,
		 * 			it should be encoded according to RFC 2047 (http://www.ietf.org/rfc/rfc2047.txt)
		 * @see #containsHeader(java.lang.String)
		 * @see #addHeader(java.lang.String, java.lang.String)
		 */
		public function setHeader($name, $value);
		
		/**
		 * Sets a response header with the given name and integer value.
		 * If the header had already been set, the new value overwrites the previous one.
		 * The containsHeader method can be used to test for the presence of a header before setting its value.
		 * 
		 * @param name
		 * 			The name of the header
		 * @param value
		 * 			The assigned integer header value
		 * @see #containsHeader(java.lang.String)
		 * @see #addIntHeader(java.lang.String, int)
		 */
		public function setIntHeader($name, int $value);
		
		/**
		 * Sets the status code for this response.
		 * This method is used to set the return status code when there is no error
		 * (for example, for the SC_OK or SC_MOVED_TEMPORARILY status codes).
		 * If this method is used to set an error code,
		 * then the container's error page mechanism will not be triggered.
		 * If there is an error and the caller wishes to invoke an error page defined in the web application,
		 * then sendError(int, java.lang.String) must be used instead.
		 * This method preserves any cookies and other response headers.
		 * Valid status codes are those in the 2XX, 3XX, 4XX, and 5XX ranges.
		 * Other status codes are treated as container specific.
		 * 
		 * @param sc The error status code
		 * @param msg The status message
		 * 
		 * @see #sendError(int, java.lang.String)
		 */
		public function setStatus($sc, $msg);
	}
} 
?>