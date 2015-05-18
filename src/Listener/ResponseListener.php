<?php

namespace SilverSnake\Listener;

use SilverSnake\Event\ResponseEvent;

interface ResponseListener {

	/**
	 * Receives notification that a Request is about to come into scope of the web application.
	 *
	 * @param ResponseEvent $e
	 */
	public function requestInitialized(ResponseEvent $event);

	/**
	 * Receives notification that a ServletRequest is about to go out
	 * of scope of the web application.
	 *
	 * @param ResponseEvent $e
	 */
	public function requestDestroyed(ResponseEvent $event);
}