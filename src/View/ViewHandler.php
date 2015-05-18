<?php

namespace SilverSnake\View;

use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Template;

class ViewHandler
{

    /**
     * @var \Twig_Loader_Chain
     */
    private $loader;

    /**
     * @var \Twig_Environment
     */
    private $env = null;

    /**
     * @var \Twig_Template
     */
    private $template;

    /**
     * @var array
     */
    private $param = array();

    public function __construct()
    {
        $this->loader = new Twig_Loader_Chain();
        $appPath = dirname(__DIR__);
        $appPath = dirname($appPath);
        $files = new Twig_Loader_Filesystem();
        $files->addPath($appPath . '/app/resources/views');
        $this->loader->addLoader($files);
    }

    public function setTemplate($path)
    {
        $path .= '.twig';
        if ($this->env === null)
            $this->env = new Twig_Environment($this->loader, array(
                'cache' => __DIR__ . '/../../app/cache'
            ));
        $this->template = $this->env->loadTemplate($path);
        return $this;
    }

    public function setParameter($name, $value)
    {
        $this->param[$name] = $value;
    }

    public function removeParameter($name)
    {
        unset($this->param[$name]);
    }

    public function generate()
    {
        return $this->template->render($this->param);
    }

}