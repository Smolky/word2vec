<?php

/**
 * index
 *
 * @package UMUWords
 */

 
// Require configuration
require_once __DIR__ . '/custom/bootstrap.php';


// Template system
Twig_Autoloader::register();



// Template system
$twig_configuration = array ();
if (PRODUCTION) {
    $twig_configuration = array (
        'cache' => __DIR__ . '/cache/templates',
        'auto_reload' => true
    );
}

$loader = new Twig_Loader_Filesystem ("templates");
$twig = new Twig_Environment ($loader, $twig_configuration);


// Add global variables to the template
$twig->addGlobal ('base_url', BASE_URL);
$twig->addGlobal ('full_url', FULL_URL);
$twig->addGlobal ('version', PRODUCTION ? VERSION : rand (1, 10000));


// Store the template system as a service
$container['loader'] = $loader;
$container['templates'] = $twig;


// Translations
$i18n = new i18n ();
$i18n->setCachePath ('./cache/lang');
$i18n->setFilePath ('./lang/lang_{LANGUAGE}.json');
$i18n->setFallbackLang ('en');
$i18n->setPrefix ('I');
$i18n->setSectionSeperator ('_');
$i18n->init();

$container['i18n'] = $i18n;


// Attach to TWIG the global language object
$i18n_function = new Twig_SimpleFunction ('__', function ($method) {
    try {
        return call_user_func ('I' . '::' . $method); 
    } catch (Exception $e) {
        return '';
    }
    
});
$twig->addFunction ($i18n_function);


// Get the authentication token
$container['api_token_credentials'] = isset ($_GET['token']) ? $_GET['token'] : null;


// Create the router
$router = new AltoRouter();
$router->setBasePath (ltrim (BASE_URL, '/'));



$container['router'] = $router;


session_start();


// Attach routers
require ('routes.php');


// match current request URL
$match = $router->match();


// Determine which controller will handle the current route
if ($match && is_callable ($match['target'])) {
    $controller = call_user_func_array ($match['target'], $match['params']);
    
} else {

    // No controller was found, using a 404 controller
    require __DIR__ . '/controllers/maintenance/NotFound404.php';
    $controller = new NotFound404 ();
    
}



// Handle the controller
echo $controller->handle ();