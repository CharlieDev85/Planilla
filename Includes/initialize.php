<?php

//ini_set('max_execution_time', 600);
// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//error_reporting(0); // Disable all errors.
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'wamp64'.DS.'www'.DS.'Planilla');
defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'Includes');
defined('LIB_BEANS') ? null : define('LIB_BEANS', LIB_PATH.DS.'Beans');

// load config file first
require_once(LIB_PATH.DS.'config.php');
// load core objects
require_once(LIB_PATH.DS.'database.php');

// load database-related classes
require_once(LIB_BEANS.DS.'bonificacion.php');
require_once(LIB_BEANS.DS.'empleado.php');
require_once(LIB_BEANS.DS.'estado.php');
require_once(LIB_BEANS.DS.'igss_salario.php');
require_once(LIB_BEANS.DS.'isr.php');
require_once(LIB_BEANS.DS.'planilla.php');
require_once(LIB_BEANS.DS.'planilla_empleado.php');

// load basic functions next so that everything after can use them
require_once(LIB_PATH.DS.'functions.php');


