<?php

namespace SilverSnake\Event;


use SilverSnake\Controller\ControllerContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestEvent
 * Events of this kind indicate lifecycle
 * events for a ServletRequest.
 * The source of the event
 * is the ServletContext of this web application.
 *
 * @package SilverSnake\Event
 * @see SilverSnake\Listener\RequestListener
 */
class RequestEvent extends Event {

	/**
	 * @var Request
	 */
	private $request;

	/** Construct a RequestEvent for the given ControllerContext
	 * and Request.
	 *
	 * @param ControllerContext $context
	 * 	the ControllerContext of the web application.
	 * @param Request $request
	 * 	The Request that is sending the event.
	 */
	public function __construct(ControllerContext $context, Request $request) {
		parent($context);
		$this->request = $request;
	}

	/**
	 * Returns the ServletRequest that is changing.
	 * @return Request
	 */
	public function getRequest(){
		return $this->request;
	}

	/**
	 * Returns the ControllerContext of this web application.
	 *
	 * @return ControllerContext
	 */
	public function getControllerContext() {
		return $this->getSource();
	}

}