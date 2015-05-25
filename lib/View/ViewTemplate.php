<?php

namespace SilverSnake\View;


class ViewTemplate
{
    /**
     * @var \SilverSnake\View\ViewHandler
     */
    private $handler;

    /**
     * @var array
     */
    private $style = array();

    /**
     * @var array
     */
    private $script = array();

    /**
     * @var bool
     */
    private static $start = false;

    /**
     * @var array
     */
    private static $css = array();

    /**
     * @var array
     */
    private static $js = array();


    private function bootstrap()
    {
        $dir = __DIR__ . '/../../config';

        $script = file_get_contents($dir . '/script.json', true);
        $script = (array)json_decode($script);
        foreach ($script as $key => $value) {
            self::$js[$key] = $value;
        }

        $style = file_get_contents($dir . '/style.json', true);
        $style = (array)json_decode($style);
        foreach ($style as $key => $value) {
            self::$css[$key] = $value;
        }

        self::$start = true;

    }
    
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        if (!self::$start)
            self::bootstrap();
        $this->handler = new ViewHandler();
        $this->handler->setTemplate($name);
    }

    /**
     * @return ViewHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $lib
     */
    public function addComponent($lib)
    {
        if (array_key_exists($lib, self::$css) && !array_key_exists($lib, $this->style)) {
            $this->style[$lib] = self::$css[$lib]['url'];
            if (array_key_exists('require', self::$css[$lib]))
                foreach (self::$css['require'] as $requirement)
                    $this->addComponent($requirement);
        }

        if (array_key_exists($lib, self::$js) && !array_key_exists($lib, $this->script)) {
            $this->script[$lib] = self::$js[$lib]['url'];
            if (array_key_exists('require', self::$js[$lib]))
                foreach (self::$js['require'] as $requirement)
                    $this->addComponent($requirement);
        }
    }

    /**
     * @param string $lib
     */
    public function removeComponent($lib)
    {
        if (array_key_exists($lib, $this->script)) {
            unset($this->script[$lib]);
            if (array_key_exists('require', self::$js[$lib]))
                foreach (self::$js['require'] as $require)
                    $this->removeComponent($require);
        }

        if (array_key_exists($lib, $this->style)) {
            unset($this->style[$lib]);
            if (array_key_exists('require', self::$css[$lib]))
                foreach (self::$css['require'] as $require)
                    $this->removeComponent($require);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setParameter($name, $value)
    {
        $this->handler->setParameter($name, $value);
    }

    /**
     * @param string $name
     */
    public function removeParameter($name)
    {
        $this->handler->removeParameter($name);
    }

    /**.
     * @return string
     */
    public function getDisplay()
    {
        $scripts = array();
        foreach ($this->script as $script) {
            $scripts = array_merge($scripts, $script);
        }
        $this->setParameter('scripts', $scripts);

        $styles = array();
        foreach ($this->style as $style) {
            $style = array_merge($styles, $style);
        }
        $this->setParameter('styles', $styles);

        return $this->handler->generate();
    }

}