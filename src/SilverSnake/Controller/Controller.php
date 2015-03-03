<?php

namespace SilverSnake\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller {

	/**
	 * @var ControllerContext
	 */
	private $context;

	public final function setControllerContext(ControllerContext $context) {
		$this->context = $context;
	}

	public function getControllerContext() {
		return $this->context;
	}

	/**
	 * Called by the kernel to indicate to a controller that the controller is being placed into service.
	 * A convenience method which can be overridden so that there's no need to call parent::init().
	 *
	 * @param ControllerContext $context
	 * 			ControllerContext of the Controller
	 */
	public function init(ControllerContext $context) {

	}

	/**
	 * Called by the kernel to indicate to a container that the container is being taken out of service.
	 */
	public function destroy() {

	}

    private static function noService(Request $request, Response $response) {
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function service(Request $request, Response $response){
        $method = $request->getMethod();
        $method = strtolower($method);
        $method = ucfirst($method);
        $method = 'do' . $method;
        $this->$method($request, $response);
    }

    protected function doGet(Request $request, Response $response) {
        self::noService($request, $response);
    }

    protected function doPost(Request $request, Response $response) {
        self::noService($request, $response);
    }

    protected function doPut(Request $request, Response $response) {
        self::noService($request, $response);
    }

	protected function doDelete(Request $request, Response $response) {
		self::noService($request, $response);
	}

    protected function doHead(Request $request, Response $response) {
        $this->doGet($request, $response);
		$response->setContent('');
    }

	protected function doOptions(Request $request, Response $response) {
		self::noService($request, $response);
	}

}