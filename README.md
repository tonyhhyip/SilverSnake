SilverSnake
============

The php library which can be included in elearn.pyc.edu.hk php server.
All the code are under Apache 2.0 license or GNU/GPL 3.
Part of the code are forking from other Open Source Project.
It is mainly for education and simple web service.

##LICENSE
You may pick the Apache License 2.0 or the GNU/GPL 3.

##Install
Directly open call a folder SilverSnake and add it to the include path of PHP.

##Quick Start
Require the init.php of SilverSnake and you can use all the package you installed.
<code>
<pre>
&lt;?php
	// Init SilverSnake
	require_once('SilverSnake/init.php');
	// Use namespace for default classes.
	use \php\lang;
</pre>
</code>

## Release
As it is mainly for education usage, it is seldom with a stable version.
To prevent bugs, there will be marking for developer Class method or function which are annotated with '@Develop'
Every class method and function would have its own annotation '@version'

## Get Involved
SilverSnake is using package managing. Static function in global is not a good pratice.
For example, to develop a package which is called php.foo, you may directly add a foo directory under php,
and create a Boo.php file which contain a class Boo with namespace php\foo only.
For other static function, you may put in a static.php file which would be include when package is imported.
To inital the package, for example, import another package, you may write the script in init.php which is run when the package finished importing.

## Special Code Source
 - [Symfony] (http://symfony.com)
