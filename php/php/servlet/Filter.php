<?php

/**
 * @package \php\servlet
 * package php.servlet;
 * @file Filter
 */
namespace \php\servlet;

/**
 * A filter is an object that performs filtering tasks on either the request to
 * a resource (a servlet or static container), or on the response from a resource,
 * or both.
 * 
 * Filters perform filtering in the <code>doFilter</code> method. Every Filter has access to 
 * a FilterConfig object from which it can obtain its initialization parameters, a reference
 * to the ServletContext which it can use, for example, to load resources needed for
 * filtering tasks.
 * 
 * Filter can configured in the deployment descriptor of a web application.
 *
 * Examples that have been identified for this design are:
 * <ul>
 * <li>Authentication Filters</li>
 * <li>Logging and Auditing Filters</li>
 * <li>Image conversion Filters</li>
 * <li>Data compression Filters</li>
 * <li>Encryption Filters</li>
 * <li>Tokenizing Filters</li>
 * <li>Filters that trigger resource access events</li>
 * <li>XSL/T filters</li>
 * <li>Mime-type chain Filter</li> 
 */
interface Filter {
	/**
	 * Called by the web container to indicate to a filter that it is being placed into service.
	 * The servlet container calls the init method exactly once after
	 * instantiating the filter. The init method must complete successfully
	 * before the filter is asked to do any filtering work.
	 * 
	 * The web container cannot place the filter into service if the init method either
	 * <br>
	 * Throws a ServletException, or<br>
	 * Does not return within a time period defined by the web container
	 */
	public function init(FilterConfig filterConfig);

	/**
	 * The <code>doFilter</code> method of the Filter is called by the container each time
	 * a request/response pair is passed through the chain due to a client request
	 * for a resource at the end of the chain. The FilterChain passed in to this method
	 * allows the Filter to pass on the request and response to the next entity in the chain.
	 * 
	 * A typical implementation of this method would follow the following pattern:
	 * <ul>
	 * <li>Examine the request</li>
	 * <li>Optionally wrap the request object with a custom implementation 
	 * to filter content or headers for input filtering.</li>
	 * <li>Optionally wrap the response object with a custom implementation to
	 * filter content or headers for output filtering</li>
	 * <li><strong>Either</strong> invoke the next entity in the chain using the FilterChain 
	 * object (<code>chain.doFilter()</code>)</li>
	 * <li><strong>Or</strong> not pass on the request/response pair to the next entity in the
	 * filter chain to block the request processing</li>
	 * Directly set headers on the response after invocation of the next entity in the filter chain.
	 */
	public function doFilter(ServletRequest request, ServletResponse response, FilterChain chain);
}

?>
