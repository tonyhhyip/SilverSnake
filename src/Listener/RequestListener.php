<?php

namespace SilverSnake\Listener;

use SilverSnake\Event\RequestEvent;

/**
 * Interface RequestListener
 * Interface for receiving notification events about requests coming
 * into and going out of scope of a web application.
 *
 * <p>A Request is defined as coming into scope of a web
 * application when it is about to enter the first servlet or filter
 * of the web application, and as going out of scope as it exits
 * the last servlet or the first filter in the chain.
 *
 * <p>In order to receive these notification events, the implementation
 * class must be either declared in the deployment descriptor of the web
 * application, or registered via one of the addListener methods defined on
 * {@link ControllerContext}.
 *
 * <p>Implementations of this interface are invoked at their
 * {@link #requestInitialized} method in the order in which they have been
 * declared, and at their {@link #requestDestroyed} method in reverse
 * order.
 *
 * @package SilverSnake\Listener
 */
interface RequestListener {

	/**
	 * Receives notification that a Request is about to come into scope of the web application.
	 *
	 * @param RequestEvent $e
	 */
	public function requestInitialized(RequestEvent $event);

	/**
	 * Receives notification that a ServletRequest is about to go out
	 * of scope of the web application.
	 *
	 * @param RequestEvent $e
	 */
	public function requestDestroyed(RequestEvent $event);
}