<?php
if (!defined("HttpServletRequest"))) {
	define("HttpServletRequest", 1);

	/**
	* The servlet container creates an HttpServletRequest object and passes it as an argument to the servlet's service methods (doGet, doPost, etc).
	*/
	interface HttpServletRequest {

		/**
		* Returns the value of the named attribute as an Object, or null if no attribute of the given name exists.
		* Attributes can be set two ways. The servlet container may set attributes to make available custom information about a request.
		* For example, for requests made using HTTPS,
		* the attribute javax.servlet.request.X509Certificate can be used to retrieve information on the certificate of the client.
		* Attributes can also be set programatically using setAttribute(java.lang.String, java.lang.Object).
		* 
		* @param name
		*			a String specifying the name of the attribute.
		* @return an Object containing the value of the attribute, or null if the attribute does not exist.
		*/
		public function getAttribute($name);

		/**
		* Returns an array containing the names of the attributes available to this request.
		* This method returns an empty array if the request has no attributes available to it.
		* 
		* @return an array of strings containing the names of the request's attributes
		*/
		public function getAttributeNames();

		/**
		* Returns the length, in bytes, of the request body and made available by the input stream,
		* or -1 if the length is not known ir is greater than Integer.MAX_VALUE.
		* For HTTP servlets, same as the value of the CGI variable CONTENT_LENGTH.
		* 
		* @return an integer containing the length of the request body or -1 if the length is not known or is greater than Integer.MAX_VALUE.
		*/
		public function getContentLength();
		
		/**
		* Returns the MIME type of the body of the request, or null if the type is not known.
		* For HTTP servlets, same as the value of the CGI variable CONTENT_TYPE.
		* 
		* @return a String containing the name of the MIME type of the request, or null if the type is not known.
		*/
		public function getContentType();

		/**
		* Returns the Internet Protocol (IP) address of the interface on which the request was received.
		*
		* @return a String containing the IP address on which the request was received.
		*/
		public function getLocalAddr();

		/**
		* Returns the host name of the Internet Protocol (IP) interface on which the request was received.
		* 
		* @return a String containing the host name of the IP on which the request was received.
		*/
		public function getLocalName();

		/**
		* Returns the Internet Protocol (IP) port number of the interface on which the request was received.
		* 
		* @return an integer specifying the port number.
		*/
		public function getLocalPort();

		/**
		* Returns the value of a request parameter as a String, or null if the parameter does not exist.
		* Request parameters are extra information sent with the request.
		* For HTTP servlets, parameters are contained in the query string or posted form data.
		* You should only use this method when you are sure the parameter has only one value.
		* If the parameter might have more than one value, use getParameterValues(java.lang.String).
		* If you use this method with a multivalued parameter, the value returned is equal to the first value in the array returned by getParameterValues.
		*
		* @param name
		* 			a String specifying the name of the parameter.
		* @return a String representing the single value of the parameter.
		* @see #getParameterValues(java.lang.String)
		*/
		public function getParameter($name);

		/**
		* Returns a key-to-value array of the parameters of this request.
		* Request parameters are extra information sent with the request.
		* For HTTP servlets, parameters are contained in the query string or posted form data.
		* 
		* @return a key-to-value array containing parameter names as keys and parameter values as map values.
		*			The keys in the parameter map are of type String. The values in the parameter map are of type String array.
		*/
		public function getParameterMap();

		/**
		* Returns an array of String objects containing the names of the parameters contained in this request.
		* If the request has no parameters, the method returns an empty array.
		* 
		* @return an String array, each String containing the name of a request parameter; or an empty array if the request has no parameters.
		*/
		public function getParameterNames();

		/**
		* Returns an array of String objects containing all of the values the given request parameter has, or null if the parameter does not exist.
		* If the parameter has a single value, the array has a length of 1.
		* 
		* @param name
		* 			a String containing the name of the parameter whose value is requested
		* @return an array of String objects containing the parameter's values
		* @see #getParameter(java.lang.String)
		*/
		public function getParameterValues($name);

		/**
		* Returns the name and version of the protocol the request uses in the form protocol/majorVersion.minorVersion, for example, HTTP/1.1.
		* For HTTP servlets, the value returned is the same as the value of the CGI variable SERVER_PROTOCOL.
		*/
		public function getProtocol();

		/**
		* Returns the Internet Protocol (IP) address of the client or last proxy that sent the request.
		* For HTTP servlets, same as the value of the CGI variable REMOTE_ADDR.
		* 
		* @return a String containing the IP address of the client that sent the request.
		*/
		public function getRemoteAddr();

		/**
		* Returns the fully qualified name of the client or the last proxy that sent the request.
		* If the engine cannot or chooses not to resolve the hostname (to improve performance),this method returns the dotted-string form of the IP address.
		* For HTTP servlets, same as the value of the CGI variable REMOTE_HOST.
		* 
		* @return a String containing the fully qualified name of the client
		*/
		public function getRemoteHost();

		/**
		* Returns the Internet Protocol (IP) source port of the client or last proxy that sent the request.
		* 
		* @return an integer specifying the port number
		*/
		public function getRemotePort();

		/**
		* Returns the name of the scheme used to make this request, for example, <code>http</code>, <code>https</code>, or <code>ftp</code>.
		*
		* @return a String containing the name of the scheme used to make this request
		*/
		public function getScheme();

		/**
		* Returns the host name of the server to which the request was sent.
		*  It is the value of the part before ":" in the Host header value, if any, or the resolved server name, or the server IP address.
		* 
		* @return a String containing the name of the server.
		*/
		public function getServerName();

		/**
		* Returns the port number to which the request was sent.
		* It is the value of the part after ":" in the Host header value, if any, or the server port where the client connection was accepted on.
		* 
		* @return an integer specifying the port number.
		*/
		public function getServerPort();

		/**
		* Removes an attribute from this request. This method is not generally needed as attributes only persist as long as the request is being handled.
		* Attribute names should follow the same conventions as package names.
		* 
		* @param name
		*		a <code>String</code> specifying the name of the attribute to remove.
		*/
		public function removeAttribute($name);

		/**
		* Stores an attribute in this request. Attributes are reset between requests.
		* Attribute names should follow the same conventions as package names.
		* If the object passed in is null, the effect is the same as calling removeAttribute(java.lang.String). 
		* 
		* @param name
		*			a String specifying the name of the attribute.
		* @param o
		*			the Object to be stored.
		*/
		public function setAttribute($name, $o);
	    
	    /**
	    * Change the session id of the current session associated with this request and return the new session id.
	    * 
	    * @return the new session id
	    */
		public function changeSessionId();

		/**
		* Returns an array containing all of the Cookie objects the client sent with this request. This method returns null if no cookies were sent.
		* 
		* @return an array of all the Cookies included with this request, or null if the request has no cookies
		*/
		public function getCookies();

		/**
		* Returns the value of the specified request header as a long value that represents a Date object.
		* Use this method with headers that contain dates, such as If-Modified-Since.
		* The date is returned as the number of milliseconds since January 1, 1970 GMT. The header name is case insensitive.
		* If the request did not have a header of the specified name, this method returns -1.
		* 
		* @param name
		* 			a String specifying the name of the header
		* @return a long value representing the date specified in the header expressed as
		* 			the number of milliseconds since January 1, 1970 GMT, or -1 if the named header was not included with the request
		*/
		public function getDateHeader($name);

		/**
		* Returns the value of the specified request header as a String.
		* If the request did not include a header of the specified name, this method returns null.
		* If there are multiple headers with the same name, this method returns the first head in the request.
		* The header name is case insensitive. You can use this method with any request header.
		* 
		* @param name
		*			a String specifying the header name
		* @return a String containing the value of the requested header, or null if the request does not have a header of that name.
		*/
		public function getHeader($name);

		/**
		* Returns all the values of the specified request header as a String array objects.
		* Some headers, such as Accept-Language can be sent by clients as several headers 
		* each with a different value rather than sending the header as a comma separated list.
		* If the request did not include any headers of the specified name, this method returns an empty array.
		* The header name is case insensitive. You can use this method with any request header.
		* 
		* @param name
		* 			a String specifying the header name
		* @return an array containing the values of the requested header. 
		*			If the request does not have any headers of that name return an empty array.
		*			If the container does not allow access to header information, return null
		*/
		public function getHeaders($name);

		/**
		* Returns the value of the specified request header as an int. If the request does not have a header of the specified name, this method returns -1.
		* The header name is case insensitive.
		* 
		* @param name
		* 			a String specifying the header name
		* @return an integer expressing the value of the request header or -1 if the request doesn't have a header of this name
		*/
		public function getIntHeader($name);

		/**
		* Returns an array of all the header names this request contains. If the request has no headers, this method returns an empty array.
		* Some servlet containers do not allow servlets to access headers using this method, in which case this method returns null
		* 
		* @return an array of all the header names sent with this request;
		*			if the request has no headers, an empty array; if the servlet container does not allow servlets to use this method, null
		*/
		public function getHeaderNames();

		/**
		* Returns the name of the HTTP method with which this request was made, 
		* for example, GET, POST, or PUT. Same as the value of the CGI variable REQUEST_METHOD.
		* 
		* @return a String specifying the name of the method with which this request was made
		*/
		public function getMethod();

		/**
		* Returns any extra path information associated with the URL the client sent when it made this request.
		* The extra path information follows the servlet path but precedes the query string and will start with a "/" character.
		* This method returns null if there was no extra path information.
		* Same as the value of the CGI variable PATH_INFO.
		* 
		* @return a String, decoded by the web container, specifying extra path information that comes
		*			after the servlet path but before the query string in the request URL;
		*			or null if the URL does not have any extra path information
		*/
		public function getPathInfo();

		/**
		* Returns any extra path information after the servlet name but before the query string, and translates it to a real path.
		* Same as the value of the CGI variable PATH_TRANSLATED.
		* If the URL does not have any extra path information, this method returns null or
		* the servlet container cannot translate the virtual path to a real path for any reason
		* (such as when the web application is executed from an archive).
		* The web container does not decode this string.
		* 
		* @return a <code>String</code> specifying the real path, or null if the URL does not have any extra path information
		*/
		public function getPathTranslated();

		/**
		* Returns the portion of the request URI that indicates the context of the request.
		* The context path always comes first in a request URI.
		* The path starts with a "/" character but does not end with a "/" character. 
		* For servlets in the default (root) context, this method returns "". The container does not decode this string.
		* It is possible that a servlet container may match a context by more than one context path.
		* 
		* @return a String specifying the portion of the request URI that indicates the context of the request
		* @see ServletContext#getContextPath()
		*/
		public function getContextPath();

		/**
		* Returns the query string that is contained in the request URL after the path.
		* This method returns null if the URL does not have a query string. Same as the value of the CGI variable QUERY_STRING.
		* 
		* @return a String containing the query string or null if the URL contains no query string. The value is not decoded by the container.
		*/
		public function getQueryString();

		/**
		* Returns the login of the user making this request, if the user has been authenticated, or null if the user has not been authenticated.
		* Whether the user name is sent with each subsequent request depends on the browser and type of authentication.
		* Same as the value of the CGI variable REMOTE_USER.
		* 
		* @return a String specifying the login of the user making this request, or null if the user login is not known
		*/
		public function getRemoteUser();

		/**
		* Returns the part of this request's URL from the protocol name up to the query string in the first line of the HTTP request.
		* The web container does not decode this String. For example:
		* <table>
		* <tr>
		* <th>First line of HTTP request</th>
		* <th>Returned Value</th>
		* </tr>
		* <tr>
		* <td>POST /some/path.html HTTP/1.1</td><td>/some/path.html</td>
		* <td>GET http://foo.bar/a.html HTTP/1.0</td><td>/a.html</td>
		* <td>/xyz?a=b HTTP/1.1</td><td>/xyz</td>
		* </table>
		* 
		* @return a String containing the part of the URL from the protocol name up to the query string.
		*/
		public function getRequestURI();

		/**
		* Reconstructs the URL the client used to make the request.
		* The returned URL contains a protocol, server name, port number, and server path, but it does not include query string parameters.
		* This method is useful for creating redirect messages and for reporting errors.
		* 
		* @return a String object containing the reconstructed URL.
		*/
		public function getRequestURL();

		/**
		* Returns the part of this request's URL that calls the servlet.
		* This path starts with a "/" character and includes either the servlet name or a path to the servlet,
		* but does not include any extra path information or a query string.
		* Same as the value of the CGI variable SCRIPT_NAME.
		* This method will return an empty string ("") if the servlet used to process this request was matched using the "/*" pattern.
		* 
		* @return a String containing the name or path of the servlet being called, as specified in the request URL,
		* decoded, or an empty string if the servlet used to process the request is matched using the "/*" pattern.
		*/
		public function getServletPath();

		/**
		* Returns the current HttpSession associated with this request or, if there is no current session and create is true, returns a new session.
		* If create is false and the request has no valid HttpSession, this method returns null.
		* To make sure the session is properly maintained, you must call this method before the response is committed.
		* 
		* @param create = null
		* 			true to create a new session for this request if necessary; false to return null if there's no current session
		* @return the HttpSession associated with this request or null if create is false and the request has no valid session
		*/
		public function getSession($create = null);
	}
}
?>