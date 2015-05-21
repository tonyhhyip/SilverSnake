<?php

namespace SilverSnake\Controller;


use SilverSnake\Database\Database;
use SilverSnake\View\ViewHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class ContextController extends Controller
{
    /**
     * @param string $name
     * @param array $context
     *
     * @return Response
     */
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

    /**
     * @param string $id
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getConnection($id)
    {
        if (!Database::$state) {
            Database::loadDataSource();
        }
        return Database::getConnection($id);
    }

    /**
     * @param string $id
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getQueryBuilder($id)
    {
        return $this->getConnection($id)->createQueryBuilder();
    }

    /**
     * @param string $url
     * @param int $status
     *
     * @return RedirectResponse
     */
    protected function redirect($url, $status = Response::HTTP_FOUND)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @param string $url
     * @param Request $request
     *
     * @return Response
     */
    protected function render($url, Request $request)
    {
        $request = Request::create(
            $url,
            $request->getMethod(),
            array_merge($request->query->all(), $request->request->all()),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent()
        );
        return $this->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST);
    }
}