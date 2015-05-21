<?php

namespace SilverSnake\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class StaticController extends Controller
{
    abstract protected function isExpire($date);

    abstract protected function getLastModified();

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function service(Request $request){
        if (!$this->isExpire($request->headers->get('If-Modified-Since')) && in_array($request->getMethod(), array('GET',
                'HEAD'))) {
            return new Response('', Response::HTTP_NOT_MODIFIED);
        }
        $response = parent::service($request);
        $response->setLastModified(new DateTime(gmdate('D, d M Y H:i:s',$this->getLastModified()) . 'GMT'));
        return $response;
    }
}