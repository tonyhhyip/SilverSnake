<?php

namespace SilverSnake\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
class RequestEvent extends Event
{

	/**
	 * @var Request
	 */
	private $request;

	/** Construct a RequestEvent for the given ControllerContext
	 * and Request.
	 *
	 * @param Request $request
	 * 	The Request that is sending the event.
	 */
	public function __construct(HttpKernelInterface $kernel, Request $request)
    {
		parent::__construct($kernel);
		$this->request = $request;
	}

	/**
	 * Returns the ServletRequest that is changing.
	 * @return Request
	 */
	public function getRequest(){
		return $this->request;
	}

    public function getKernel()
    {
        return $this->getSource();
    }
}