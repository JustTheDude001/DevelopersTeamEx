<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('CET');

// defines the web root
define('WEB_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/index.php')));
// defindes the path to the files
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
// defines the cms path
//CMS = Content Management System
define('CMS_PATH', ROOT_PATH . '/lib/base/');

// starts the session
session_start();

// includes the system routes. Define your own routes in this file
include(ROOT_PATH . '/config/routes.php');

//include_once /includes/functions.php
//include_once(ROOT_PATH . '/includes/functions.php')
include_once(ROOT_PATH . '/includes/functions.php');

//Workaround untill I discover how the structure works properly
//I must use view somehow but I do not see how --'
include_once(ROOT_PATH . '/app/views/scripts/index/auxiliar.php');

//For mongoDB
require_once(ROOT_PATH . '/vendor/autoload.php');

/**
 * Standard framework autoloader
 * @param string $className
 */
function autoloader($className) {
	// controller autoloading
	if (strlen($className) > 10 && substr($className, -10) == 'Controller') {
		if (file_exists(ROOT_PATH . '/app/controllers/' . $className . '.php') == 1) {
			require_once ROOT_PATH . '/app/controllers/' . $className . '.php';
		}
	}
	else {
		if (file_exists(CMS_PATH . $className . '.php')) {
			require_once CMS_PATH . $className . '.php';
		}
		else if (file_exists(ROOT_PATH . '/lib/' . $className . '.php')) {
			require_once ROOT_PATH . '/lib/' . $className . '.php';
		}
		else {
			//Work around to avoid errors....
			if(file_exists(ROOT_PATH . '/app/models/'.$className.'.php')){
				require_once ROOT_PATH . '/app/models/'.$className.'.php';
			}
		}
	}
}

// activates the autoloader
// SPL = Standard PHP Libray
//spl_autoload_register allows developers to add methods to a list of autoloaders. Useful to add third-party libraries, each with their own individual autoloaders.
//
spl_autoload_register('autoloader');
//Yup my model:
//spl_autoload_register('JSONModel');


$router = new Router();
// $routes variable is taken from file routes.php, it is an array defining a key matching an URL and a controller#action to execute.
$router->execute($routes);
