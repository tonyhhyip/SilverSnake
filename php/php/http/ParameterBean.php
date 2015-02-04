<?php

namespace php\http;

/**
 * ParameterBean is a container for key/value pairs.
 *
 * @package php\http
 * @api
 */
class ParameterBean implements \IteratorAggregate, \Countable {
    /**
     * Parameter storage.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters An array of parameters.
     */
    public function __construct(array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     *
     * @return array An array of parameters
     */
    public function getAllParameters() {
        return $this->parameters;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function getParameterNames() {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array $parameters An array of parameters
     */
    public function replace(array $parameter) {
        $this->parameters = $parameter;
    }

    /**
     * Add parameter.
     *
     * @param mixed $key the key.
     * @param mixed $value the value.
     */
    public function setParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Returns a parameter by name.
     *
     * @param string $path    The key
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return mixed
     *
     * @throws \php\http\HttpException
     */
    public function getParameter($path, $default = null, $deep = false) {
        if (!$deep || false === $pos = strpos($path, '['))
            return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;

        $root = substr($path, 0 ,$pos);
        if (!array_key_exists($root, $this->parameters)) {
            return $default;
        }

        $value = $this->parameters[$root];
        $key = null;
        for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
            $char = $path[$i];

            switch($char) {
                case '[':
                    if (null !== $key)
                        throw new HttpException(__CLASS__ . ' ' . __METHOD__ . ' error on line '
                        . __LINE__ . ' in ' . __FILE__,
                            new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i)));
                    $key = '';
                    break;
                case ']':
                    if (null === $key)
                        throw new HttpException(__CLASS__ . ' ' . __METHOD__ . ' error on line '
                            . __LINE__ . ' in ' . __FILE__,
                            new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i)));

                    if (!is_array($value) || !array_key_exists($key, $value))
                        return $default;

                    $value = $value[$key];
                    $key = null;
                    break;
                default:
                    if (null === $key)
                        throw new HttpException(__CLASS__ . ' ' . __METHOD__ . ' error on line '
                            . __LINE__ . ' in ' . __FILE__,
                            new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i)));
                    $key .= $char;
            }
        }

        if (null !== $key)
            throw new HttpException(__CLASS__ . ' ' . __METHOD__ . ' error on line '
                . __LINE__ . ' in ' . __FILE__,
                new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".')));

        return $value;
    }


    /**
     * Returns true if the parameter is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the parameter exists, false otherwise
     *
     * @api
     */
    public function hasParameter($key) {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Adds parameters.
     *
     * @param array $parameters An array of parameters
     *
     * @api
     */
    public function addParameter(array $parameters = array()) {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    /**
     * Removes a parameter.
     *
     * @param string $key The key
     *
     * @api
     */
    public function remove($key) {
        unset($this->parameters[$key]);
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator() {
        return new \ArrayIterator($this->getAllParameters());
    }

    /**
     * Returns the number of parameters.
     *
     * @return int The number of parameters
     */
    public function count() {
        return count($this->getAllParameters());
    }

}