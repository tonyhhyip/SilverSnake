<?php
/**
 * @package \php\lang
 * package php.lang;
 */
namespace php\lang;

/**
 * @file
 * exceptions thrown by reflective operations
 */

/**
 * Common superclass of exceptions thrown by reflective operations in
 * core reflection.
 *
 * @see LinkageError
 */
class ReflectiveOperationException extends \php\lang\Exception {
	/**
	 * Constructs a new exception with the specified detail message
	 * and cause.
	 *
	 * <p>Note that the detail message associated with
	 * {@code cause} is <em>not</em> automatically incorporated in
	 * this exception's detail message.
	 *
	 * @param  message the detail message (which is saved for later retrieval
	 *         by the {@link #getMessage()} method).
	 * @param  cause the cause (which is saved for later retrieval by the
	 *         {@link #getCause()} method).  (A {@code null} value is
	 *         permitted, and indicates that the cause is nonexistent or
	 *         unknown.)
	 */
	public function __construct($message = "", $cause = null) {
		parent($message, $cause);
	}
}
?>