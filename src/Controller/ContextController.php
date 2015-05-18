<?php

namespace SilverSnake\Controller;


use SilverSnake\Database\Database;
use SilverSnake\View\ViewHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ContextController extends Controller
{
    protected function twig($name, array $context)
    {
        $view = new ViewHandler();
        $view->setTemplate($name);
        foreach ($context as $key => $value)
            $view->setParameter($key, $value);
        $response = new Response($view->generate(), Response::HTTP_OK, array(
            'Content-Type' => 'text/html; charset=UTF-8'
        ));
        return $response;
    }

    protected function getConnection($id)
    {
        if (!Database::$state) {
            Database::loadDataSource();
        }
        return Database::getConnection($id);
    }

    protected function getQueryBuilder($id)
    {
        return $this->getConnection($id)->createQueryBuilder();
    }

    protected function redirect($url, $status = Response::HTTP_FOUND)
    {
        return new RedirectResponse($url, $status);
    }
}