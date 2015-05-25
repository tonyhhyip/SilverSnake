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

    /**
     * @throws \Twig_Error_Loader
     */
    public function __construct()
    {
        $this->loader = new Twig_Loader_Chain();
        $appPath = dirname(__DIR__);
        $appPath = dirname($appPath);
        $files = new Twig_Loader_Filesystem();
        $files->addPath($appPath . '/app/resources/views');
        $this->loader->addLoader($files);
    }

    /**
     * @param string $path
     */
    public function setTemplate($path)
    {
        $path .= '.twig';
        if ($this->env === null)
            $this->env = new Twig_Environment($this->loader, array(
                'cache' => __DIR__ . '/../../app/cache'
            ));
        $this->template = $this->env->loadTemplate($path);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setParameter($name, $value)
    {
        $this->param[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function removeParameter($name)
    {
        unset($this->param[$name]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generate()
    {
        return $this->template->render($this->param);
    }

}