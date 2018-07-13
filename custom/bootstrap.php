<?php

use Pimple\Container;
use Symfony\Component\Debug\Debug;

/**
 * UMUWords
 *
 * This file is the bootstrap of the application and shares
 * the common functionality to both CLI, API Rest and 
 * test suite
 */

// Config PHP
set_time_limit (0);
ini_set ('default_charset', 'UTF-8');
mb_internal_encoding ('UTF-8');
mb_regex_encoding ('UTF-8');
iconv_set_encoding ('internal_encoding', 'UTF-8');
iconv_set_encoding ('output_encoding', 'UTF-8');


/** @const DIR __DIR__ */
define ('BASE_DIR', __DIR__ . '/../');


/** @const TEMP_DIR String */
define ('TEMP_DIR', BASE_DIR . 'temp/');


// Require configuration
require BASE_DIR . 'config.php';


/** @const COLLATION String */
define ('COLLATION', 'utf8');


/** @const ISO String */
define ('ISO', 'es');

 
/** @const PRODUCTION boolean Sets wheter the production environment is avaiable */
define ('PRODUCTION', $production);


/** @const BASE_URL String The base url of the application */
define ('BASE_URL', $base_url);


/** @const VERSION String The version of the application */
define ('VERSION', 0.1);


/** @const FULL_URL String The full URL */
define ('FULL_URL', (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . $base_url);


// Set the error level based on the stage
if (PRODUCTION) {
    error_reporting (0);
    ini_set('display_errors', 0);
} else {
    ini_set ('display_errors', 1);
    ini_set ('display_startup_errors', 1);
    error_reporting (E_ALL); 
}


// Require vendor
require_once BASE_DIR . 'vendor/autoload.php';


// Activate Debug
if ( ! PRODUCTION) {
    Debug::enable ();
}


/** @var $container Container A dependency container */
$container = new Container ();


// Require core libs
require (BASE_DIR . 'custom/functions.php');