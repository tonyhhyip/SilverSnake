<?php
/**
 * @package \php\lang
 * package php.lang;
 */
namespace php\lang;

/**
 * @file
 * The ClassLoader to autoloading the class.
 */

/**
 * Class autoloader.
 *
 * @package php.lang
 * @license Apache License 2.0
 * @version v0.0.3
 */
class ClassLoader {
	
	protected $parent;
	protected $autoload = false;
	protected $classes = array();
	protected $includePath = array();
	
	/**
	 * Create a new ClassLoader.
	 * If its parent is null, the constructor will auto look for the first autoload function as its parent. 
	 * @param ClassLoader $parent The parent ClassLoader.
	 */
	public function __construct(ClassLoader $parent = null) {
		if ($parent instanceof ClassLoader) {
			$this->parent = $parent;
		} else if (spl_autoload_functions() !== false) {
			$autoload = spl_autoload_functions()[0];
		} else if (function_exists('__autoload')) {
			$this->autoload = true;
		}
	}
	
	/**
	 * Define a source file of a class.
	 * 
	 * @param string $class The name of the class.
	 * @param string $src The source file.
	 */
	public function defineClass($class, $src) {
		$class = str_replace("\\", ".", $class);
		$this->classes[$class] = $src;
	}
	
	/**
	 * Loads the class with the specified name.
	 * 
	 * @param string $name The name of the class
	 */
	public function loadClass($name) {
		if (function_exists("__autoload")) {
			__autoload($name);
		}
		$name = str_replace("\\", ".", $name);
		if (isset($this->classes[$name])) {
			$src = $this->classes[$name];
			include_once($src);
		}
	}
}
?>
