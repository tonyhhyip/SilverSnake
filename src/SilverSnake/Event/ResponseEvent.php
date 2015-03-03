<?php

namespace SilverSnake\Event;

use Symfony\Component\HttpFoundation\Response;

class ResponseEvent extends Event {

	/**
	 * @var Response
	 */
	private $response;

	/** Construct a ResponseEvent for the given ControllerContext
	 * and Response.
	 *
	 * @param ControllerContext $context
	 * 	the ControllerContext of the web application.
	 * @param Response $Response
	 * 	The Response that is sending the event.
	 */
	public function __construct(ControllerContext $context, Response $Response) {
		parent($context);
		$this->response = $Response;
	}

	/**
	 * Returns the ServletResponse that is changing.
	 * @return Response
	 */
	public function getResponse(){
		return $this->response;
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