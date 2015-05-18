<?php

namespace SilverSnake\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{

	/**
	 * Called by the kernel to indicate to a controller that the controller is being placed into service.
	 * A convenience method which can be overridden so that there's no need to call parent::init().
	 */
	public function init()
    {
	}

	/**
	 * Called by the kernel to indicate to a container that the container is being taken out of service.
	 */
	public function destroy()
    {
    }

    /**
     * @return Response
     */
    private static function noService()
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function service(Request $request){
        $method = $request->getMethod();
        $method = strtolower($method);
        $method = ucfirst($method);
        $method = 'do' . $method;
        return $this->$method($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doGet(Request $request) {
        return self::noService();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doPost(Request $request) {
        return self::noService();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doPut(Request $request) {
       return self::noService();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
	protected function doDelete(Request $request) {
		return self::noService();
	}

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doHead(Request $request) {
        $response = $this->doGet($request);
		$response->setContent('');
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
	protected function doOptions(Request $request) {
		return self::noService();
	}

}