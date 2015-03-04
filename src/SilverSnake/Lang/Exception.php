<?php

namespace SilverSnake\Lang;

/**
 * The class {@code Exception} and its subclasses are a form of
 * {@code \Exception} that indicates conditions that a reasonable application might want to catch.
 *
 * The class {@code Exception} and any subclasses
 * that are not also subclasses of {@link RuntimeException} are <em>checked exceptions</em>.
 *
 * Checked exceptions need to be declared in a method or
 * constructor's {@code throws} clause if they can be thrown
 * by the execution of the method or constructor and
 * propagate outside the method or constructor boundary.
 *
 */
class Exception extends \Exception {

	protected $stackTrace = array();

	/**
	 * Constructs a new exception with {@code null} as its detail message.
	 * The cause is not initialized, and may subsequently be initialized by a
	 * call to {@link #initCause}.
	 * @param string $message the detail message (which is saved for later retrieval
     *         by the {@link #getMessage()} method).
     * @param Exception $cause
     * 			the cause (which is saved for later retrieval by the
     *         {@link #getCause()} method).  (A <tt>null</tt> value is
     *         permitted, and indicates that the cause is nonexistent or
     *         unknown.)
	 */
	public function __construct($message = "", Exception $cause = null) {
		parent($message, 0, $cause);
		if (strlen($message) ==  0 && $cause != null)
			$this->message = $cause->getMessage();
		while ($cause != null) {
			$this->stackTrace[] = $cause;
			$cause = $cause->getCause();
		}
	}

	/**
	 * Returns the cause of this throwable or {@code null} if the
     * cause is nonexistent or unknown.  (The cause is the throwable that
     * caused this throwable to get thrown.)
     *
     * <p>This implementation returns the cause that was supplied via one of
     * the constructors requiring a {@code Throwable}, or that was set after
     * creation with the {@link #initCause(Throwable)} method.  While it is
     * typically unnecessary to override this method, a subclass can override
     * it to return a cause set by some other means.  This is appropriate for
     * a "legacy chained throwable" that predates the addition of chained
     * exceptions to {@code Throwable}.  Note that it is <i>not</i>
     * necessary to override any of the {@code PrintStackTrace} methods,
     * all of which invoke the {@code getCause} method to determine the
     * cause of a throwable.
     *
     * @return  the cause of this throwable or {@code null} if the
     *          cause is nonexistent or unknown.
	 */
	public function getCause() {
		return $this->getPrevious();
	}

	/**
	 * Prints this throwable and its backtrace to the
     * standard error stream. This method prints a stack trace for this
     * {@code Throwable} object on the error output stream that is
     * the value of the field {@code System.err}. The first line of
     * output contains the result of the {@link #toString()} method for
     * this object.  Remaining lines represent data previously recorded by
     * the method {@link #fillInStackTrace()}.
	 */
	public function printStackTrace() {
		println($this);
		foreach ($this->stackTrace as $stack) {
			println("\tat $stack");
		}

	}

	/**
	 * (non-PHPdoc)
	 * @see Exception::__toString()
	 */
	public function __toString() {
		$s = __CLASS__;
		$e = $this;
		$msg = sprintf("%s:%d %s [%s]\n", $e->getFile(), $e->getLine(), $e->getMessage(), __CLASS__);
		return $msg != "" ? ($s . ": " . $msg) : $s;
	}
}