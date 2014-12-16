<?php
if (!defined("RESTfulSession")) {
  define("RESTfulSession");
  require_once("Servlet/HttpSession.php");
  class RESTfulSession implements HttpSession {
    // Data Store in Session.
    private $sessionData = array();
    // Time To Live.
    private $interval;
    // Session Cookie Name
    private $sessionName;

    /**
    * Get load the Session Data.
    */
    public function __construct($data = array()) {
      if (!isset($_SESSION)) {
        session_start();
      }
      // Get the session cookie parameters
      $sessionCookie = session_get_cookie_params();
      $this->interval = $sessionCookie["lifetime"];

      // TODO prevent from using ini_get.
      $this->sessionName = ini_get("session.name");
      $this->sessionData = $_SESSION;
      foreach ($data as $name => $value) {
        $this->sessionData[$name] = $value;
      }
    }

    /**
    * Returns the object bound with the specified name in this session, or null if no object is bound under the name.
    * 
    * @param name
    *       a string specifying the name of the object.
    * @return the object with the specified name.
    */
    public function getAttribute($name) {
      return isset($this->sessionData[$name]) ? $this->sessionData[$name] : null;
    }

    /**
    * Returns an array of String objects containing the names of all the objects bound to this session.
    *
    * @return an array of String objects specifying the names of all the objects bound to this session.
    */
    public function getAttributeNames() {
      return array_keys($this->sessionData);
    }

    /**
    * Returns the time when this session was created, measured in milliseconds since midnight January 1, 1970 GMT.
    *
    * @return a long specifying when this session was created, expressed in milliseconds since 1/1/1970 GMT
    */
    public function getCreationTime() {
      return $_SERVER["REQUEST_TIME"];
    }

    /**
    * Returns a string containing the unique identifier assigned to this session.
    * The identifier is assigned by the php zend server and is implementation dependent.
    * 
    * @return a string specifying the identifier assigned to this session
    */
    public function getId() {
      return session_id();
    }

    /**
    * Returns the maximum time interval, in seconds, that the servlet container will keep this session open between client accesses.
    * After this interval, the servlet container will invalidate the session.
    * The maximum time interval can be set with the setMaxInactiveInterval method.
    * 
    * @return an integer specifying the number of seconds this session remains open between client requests
    *
    * @see #setMaxInactiveInterval(int)
    */
    public function getMaxInactiveInterval() {
      return $this->interval;
    }

    /**
    * Returns true if the client does not yet know about the session or if the client chooses not to join the session.
    * For example, if the server used only cookie-based sessions, and the client had disabled the use of cookies,
    * then a session would be new on each request.
    * 
    * @return <code>true</code> if the server has created a session, but the client has not yet joined
    */
    public function isNew() {
      // FIXME better implements.
      return count($this->sessionData) > 0;
    }

    /**
    * Removes the object bound with the specified name from this session.
    * If the session does not have an object bound with the specified name, this method does nothing.
    * 
    * @param name
    *       the name of the object to remove from this session
    */
    public function removeAttribute($name) {
      unset($this->sessionData[$name]);
      unset($_SESSION[$name]);
    }

    /**
    * Binds an object to this session, using the name specified.
    * If an object of the same name is already bound to the session, the object is replaced.
    * If the value passed in is null, this has the same effect as calling removeAttribute().
    * 
    * @param name
    *       the name to which the object is bound; cannot be null
    * @param value
    *       the object to be bound
    */
    public function setAttribute($name, $value) {
      $this->sessionData[$name] = $value;
      $_SESSION[$name] = $value;
    }

    /**
    * Specifies the time, in seconds, between client requests before the servlet container will invalidate this session.
    * An interval value of zero or less indicates that the session should never timeout.
    * 
    * @param interval
    *      An integer specifying the number of seconds
    */
    public function setMaxInactiveInterval($interval) {
      $interval = intval($interval);
      session_set_cookie_params($interval <= 0 ? time() + $interval : -1);
    }
  }
}
?>