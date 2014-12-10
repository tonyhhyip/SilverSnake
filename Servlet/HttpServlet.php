<?php
if (!define("HttpServlet")) {
  define("HttpServlet", 1);

  require_once("Servlet/HttpServletRequest.php");
  require_once("Servlet/HttpServletResponse.php");

  /**
  * @api
  * Provides an abstract class to be subclassed to create an HTTP servlet suitable for a Web site.
  * A subclass of HttpServlet must override at least one method, usually one of these:
  * <ul>
  * <li>doGet, if the servlet supports HTTP GET requests</li>
  * <li>doPost, for HTTP POST requests</li>
  * <li>doPut, for HTTP PUT requests</li>
  * <li>doDelete, for HTTP DELETE requests</li>
  * <li>init and destroy, to manage resources that are held for the life of the servlet</li>
  * <li>getServletInfo, which the servlet uses to provide information about itself</li>
  * </ul>
  * There's almost no reason to override the service method.
  * Service handles standard HTTP requests by dispatching them to the handler methods for each HTTP request type (the doXXX methods listed above).
  * Likewise, there's almost no reason to override the doOptions and doTrace methods.
  * Servlets typically run on multithreaded servers,
  * so be aware that a servlet must handle concurrent requests and be careful to synchronize access to shared resources.
  * Shared resources include in-memory data such as instance or class variables and external objects such as files, database connections, and network connections.
  *
  * @author Tony Yip
  * @see HttpServlet(Java EE)
  * @version 0.0.3-beta
  */
  abstract class HttpServlet {

    // HTTP Methods.
    const METHOD_DELETE = "DELETE";
    const METHOD_HEAD = "HEAD";
    const METHOD_GET = "GET";
    const METHOD_OPTIONS = "OPTIONS";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_TRACE = "TRACE";
      
    const HEADER_IFMODSINCE = "If-Modified-Since";
    const HEADER_LASTMOD = "Last-Modified";

    /**
    * Called by the server (via the service method) to allow a servlet to handle a DELETE request.
    * The DELETE operation allows a client to remove a document or Web page from the server.
    * This method does not need to be either safe or idempotent.
    * Operations requested through DELETE can have side effects for which users can be held accountable.
    * When using this method, it may be useful to save a copy of the affected URL in temporary storage.
    * If the HTTP DELETE request is incorrectly formatted, doDelete returns an HTTP "Bad Request" message. 
    *
    * @param req
    *    The HttpServletRequest object that contains the request the client made of the servlet.
    * @param resp
    *     The HttpServletResponse object that contains the response the servlet returns to the client
    */
    protected function doDelete($req, $resp) {
      $this->notAllow($req, $resp);
    }

    /**
    * Called by the server (via the service method) to allow a servlet to handle a GET request.
    * Overriding this method to support a GET request also automatically supports an HTTP HEAD request.
    * A HEAD request is a GET request that returns no body in the response, only the request header fields.
    * When overriding this method, read the request data, write the response headers,
    * get the response's writer or output stream object, and finally, write the response data.
    * It's best to include content type and encoding.
    * When using a PrintWriter object to return the response, set the content type before accessing the PrintWriter object.
    * 
    * The servlet container must write the headers before committing the response, because in HTTP the headers must be sent before the response body.
    * 
    * Where possible, set the Content-Length header (with the ServletResponse.setContentLength(int) method),
    * to allow the servlet container to use a persistent connection to return its response to the client,
    * improving performance.
    * The content length is automatically set if the entire response fits inside the response buffer.
    * 
    * When using HTTP 1.1 chunked encoding (which means that the response has a Transfer-Encoding header), do not set the Content-Length header.
    * 
    * The GET method should be safe, that is, without any side effects for which users are held responsible. 
    * For example, most form queries have no side effects. If a client request is intended to change stored data, the request should use some other HTTP method.
    *
    * The GET method should also be idempotent, meaning that it can be safely repeated. 
    * Sometimes making a method safe also makes it idempotent.
    * For example, repeating queries is both safe and idempotent, but buying a product online or modifying data is neither safe nor idempotent.
    * 
    * If the request is incorrectly formatted, doGet returns an HTTP "Bad Request" message.
    * 
    * @param req
    *      The request object that is passed to the servlet.
    * @param resp
    *       an HttpServletResponse object that contains the response the servlet sends to the client.
    * @see ServletResponse#setContent(java.lang.String)
    */
    protected function doGet($req, $resp) {
      notAllow($req, $resp);
    }

    /**
    * Receives an HTTP HEAD request from the protected service method and handles the request.
    * The client sends a HEAD request when it wants to see only the headers of a response, such as Content-Type or Content-Length.
    * The HTTP HEAD method counts the output bytes in the response to set the Content-Length header accurately.
    * 
    * If you override this method, you can avoid computing the response body and just set the response headers directly to improve performance. 
    * Make sure that the doHead method you write is both safe and idempotent
    * (that is, protects itself from being called multiple times for one HTTP HEAD request).
    * 
    * If the HTTP HEAD request is incorrectly formatted, doHead returns an HTTP "Bad Request" message.
    * 
    * @param req
    *       The request object that is passed to the servlet.
    * @param resp
    *       The response object that the servlet uses to return the headers to the client.
    */
    protected function doHead($req, $resp) {
      $this->doGet($req, $resp);
      ob_clean();
    }

    /**
    * Called by the server (via the service method) to allow a servlet to handle a OPTIONS request.
    * The OPTIONS request determines which HTTP methods the server supports and returns an appropriate header.
    * For example, if a servlet overrides doGet, this method returns the following header:
    * <code>Allow: GET, HEAD, TRACE, OPTIONS</code>
    * There's no need to override this method unless the servlet implements new HTTP methods, beyond those implemented by HTTP 1.1.
    * 
    * @param req
    *       The request object that is passed to the servlet.
    * @param resp
    *      The response object that the servlet uses to return the headers to the client.
    */
    protected function doOptions($req, $resp) {
      $resp->setHeader("Allow", "OPTIONS");
    }

    /**
    * Called by the server (via the service method) to allow a servlet to handle a POST request.
    * The HTTP POST method allows the client to send data of unlimited length to the Web server a single time
    * and is useful when posting information such as credit card numbers.
    * 
    * When overriding this method, read the request data, write the response headers, get the response's writer or output stream object,
    * and finally, write the response data. It's best to include content type and encoding. 
    * When using a PrintWriter object to return the response, set the content type before accessing the PrintWriter object.
    * 
    * The servlet container must write the headers before committing the response, because in HTTP the headers must be sent before the response body.
    * 
    * Where possible, set the Content-Length header (with the ServletResponse.setContentLength(int) method), 
    * to allow the servlet container to use a persistent connection to return its response to the client, improving performance.
    * The content length is automatically set if the entire response fits inside the response buffer.
    * 
    * When using HTTP 1.1 chunked encoding (which means that the response has a Transfer-Encoding header), do not set the Content-Length header.
    * 
    * This method does not need to be either safe or idempotent.
    * Operations requested through POST can have side effects for which the user can be held accountable,
    * for example, updating stored data or buying items online.
    * 
    * If the HTTP POST request is incorrectly formatted, doPost returns an HTTP "Bad Request" message.
    * 
    * @param req
    *       The request object that is passed to the servlet.
    * @param resp
    *      The response object that the servlet uses to return the headers to the client.
    * @see ServletOutputStream
    * @see ServletResponse#setContentType(java.lang.String)
    */
    protected function doPost($req, $resp) {
      notAllow($req, $resp);
    }

    /**
    * Called by the server (via the service method) to allow a servlet to handle a PUT request.
    * The PUT operation allows a client to place a file on the server and is similar to sending a file by FTP.
    * 
    * When overriding this method, leave intact any content headers sent with the request
    * (including Content-Length, Content-Type, Content-Transfer-Encoding, Content-Encoding, Content-Base, Content-Language, Content-Location, 
    * Content-MD5, and Content-Range).
    * If your method cannot handle a content header, it must issue an error message (HTTP 501 - Not Implemented) and discard the request.
    * For more information on HTTP 1.1, see RFC 2616 .
    * 
    * This method does not need to be either safe or idempotent.
    * Operations that doPut performs can have side effects for which the user can be held accountable.
    * When using this method, it may be useful to save a copy of the affected URL in temporary storage.
    *
    * If the HTTP PUT request is incorrectly formatted, doPut returns an HTTP "Bad Request" message.
    *
    * @param req
    *       The request object that is passed to the servlet.
    * @param resp
    *      The response object that the servlet uses to return the headers to the client.
    */
    protected function doPut($req, $resp) {
      notAllow($req, $resp);
    }

    /**
    * Called by the server (via the service method) to allow a servlet to handle a TRACE request.
    * A TRACE returns the headers sent with the TRACE request to the client, so that they can be used in debugging.
    * There's no need to override this method.
    * 
    * @param req
    *       The request object that is passed to the servlet.
    * @param resp
    *      The response object that the servlet uses to return the headers to the client.
    */
    protected function doTrace($req, $resp) {
      // TODO improve it
      notAllow($req, $resp);
    }

    /**
    * Returns the time the HttpServletRequest object was last modified,  in milliseconds since midnight January 1, 1970 GMT.
    * If the time is unknown, this method returns a negative number (the default).
    * Servlets that support HTTP GET requests and can quickly determine their last modification time should override this method.
    * This makes browser and proxy caches work more effectively, reducing the load on server and network resources.
    * 
    * @param req
    *       The HttpServletRequest object that is sent to the servlet.
    * @return
    *     a long integer specifying the time the HttpServletRequest object was last modified, in milliseconds since midnight, January 1, 1970 GMT,
    *     or -1 if the time is not known
    */
    protected function getLastModified($req) {
      return -1;
    }

    /**
    * Dispatches client requests to the doXXX methods defined in this class.
    * This method is an HTTP-specific version of the Servlet.service(javax.servlet.ServletRequest, javax.servlet.ServletResponse) method.
    * There's no need to override this method.
    * 
    * @param req
    *       The request object that is passed to the servlet.
    * @param resp
    *      The response object that the servlet uses to return the headers to the client.
    * @see Servlet#service(javax.servlet.ServletRequest, javax.servlet.ServletResponse)
    */
    public function service($req, $resp) {
      $method = $req->getMethod();
      switch ($method) {
        case HttpServlet::METHOD_GET:
          $lastModified = $this->getLastModified($req);
          if ($lastModified <= -1) {
            $this->doGet($req, $resp);
          } else {
            $ifModifiedSince =$req->getHeader(HttpServlet::HEADER_IFMODSINCE);
            if ($ifModifiedSince < $lastModified) {
              $resp->setDateHeader(HttpServlet::HEADER_LASTMOD, $lastModified);
              $this->doGet($req, $resp);
            } else {
              $resp->setStatus(HttpServletResponse::SC_NOT_MODIFIED);
            }
          }
        break;
        
        case HttpServlet::METHOD_HEAD:
          $lastModified = $this->getLastModified($req);
          $resp->setDateHeader(HttpServlet::HEADER_LASTMOD, $lastModified);
        case HttpServelt::METHOD_POST:
        case HttpServelt::METHOD_PUT:
        case HttpServelt::METHOD_DELETE:
        case HttpServelt::METHOD_OPTIONS:
        case HttpServelt::METHOD_TRACE:
          $method = strtolower($method);
          $method = "do" . ucfirst($method);
          $this->$method($req, $resp);
        break;

        default:
          ob_clean();
          $resp->setStatus(HttpServletResponse::SC_NOT_IMPLEMENTED);
      }
    }

    private function notAllow($req, $resp) {
      $protocol = $req->getProtocol();
      $resp->setStatus(HttpServletResponse::SC_METHOD_NOT_ALLOWED);
    }
  }
}
?>