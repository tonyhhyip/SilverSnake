<?php
/**
 * @package phpLib
 */
namespace \php\lang;


/**
 * Class autoloader.
 *
 * @package phpLib
 * @license http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE, Version 3
 * @version v0.0.2
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
	 * Regist the ClassLoader for autoload.
	 */
	public function regist() {
		spl_autoload_register(array(__CLASS__, 'loadClass'));
	}
	
	/**
	 * Unregist the ClassLoader for autoload.
	 */
	public function unregist() {
		spl_autoload_unregister(array(__CLASS__, 'loadClass'));
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
		$name = str_replace("\\", ".", $class);
		if (isset($this->classes[$name])) {
			$src = $this->classes[$name];
			foreach ($this->includePath as $path) {
				if (file_exists('$path/$src')) {
					include_once('$path/$src');
					return;
				}
			}
		}
	}
	
	/**
	 * Add an include path of the source files.
	 *  
	 * @param string $path The include path.
	 */
	public function addIncludePath($path) {
		if (array_search($path, $this->includePath) === false) {
			array_push($this->includePath, $path);
		}
	}
	
	/**
	 * Add an includePath of the source files.
	 * 
	 * @parm string $path The include path.
	 */
	public function removeIncludePath($path) {
		$this->includePath = array_diff($this->includePath, array($path));
	}
	
	/**
	 * Get all the include path.
	 * 
	 * @return string[] All the include path. 
	 */
	public function getIncludePath() {
		return clone $this->includePath;
	}
}
?>