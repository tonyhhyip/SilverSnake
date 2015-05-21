<?php

namespace SilverSnake\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseEvent extends Event {

	/**
	 * @var Response
	 */
	private $response;

	/** Construct a ResponseEvent for the given ControllerContext
	 * and Response.
	 *
	 * @param  $context
	 * 	the ControllerContext of the web application.
	 * @param Response $response
	 * 	The Response that is sending the event.
	 */
	public function __construct(HttpKernelInterface $context, Response $response) {
		parent::__construct($context);
		$this->response = $response;
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
	 * @return HttpKernelInterface
	 */
	public function getKernel() {
		return $this->getSource();
	}

}