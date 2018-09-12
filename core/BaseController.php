<?php

namespace CoreOGraphy;

use \Zend\Diactoros\ServerRequestFactory;
use \Zend\Diactoros\Response;
use \Zend\Diactoros\Response\SapiEmitter;


/**
 * BaseController
 *
 * @package Core-o-Graphy
 */
abstract class BaseController {

    /** @var $_request */
    protected $_request;
    

    /** @var $_template */
    protected $_template;
    
    
    /** @var $_container */
    protected $_container;
    
    
    /** @var response */
    protected $_response;
    
    
    /**
     * handleRequest
     *
     * This method has to be implemented by the controllers
     *
     * @package Core-o-Graphy
     */
    
    public abstract function handleRequest ();
    
    
    
    /**
     * __construct
     *
     * @package Core-o-Graphy
     */
    public function __construct () {
        
        // Reference container
        global $container;
        
        
        // Store
        $this->_container = $container;
        
        
        // Create the request
        $this->_request = ServerRequestFactory::fromGlobals ($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        
       
        // Get class info for the current controller
        $class_info = new \ReflectionClass ($this);
        $class_path = dirname ($class_info->getFileName()); 
        $class_path = str_replace (getcwd (), '', $class_path);
        $class_path = trim ($class_path, '/');
        
        
        // Fetch template system
        if ($container['templates']) {
        
            $twig = $container['templates'];
            $loader = $container['loader'];
            
            if (is_dir ($class_path . '/templates/')) {
                $loader->addPath ($class_path . '/templates/');
            }     

            // Store
            $this->_template = $twig;
        
        }
        
        
        // Create response
        $this->_response = new Response ();

    }
    
    
    /**
     * handles
     *
     * @package Core-o-Graphy
     */
    
    public function handle () {
    
        // Handle request
        $this->handleRequest ();
    
        
        // Create the response
        $emitter = new SapiEmitter();
        $emitter->emit ($this->_response);
    }
    
}
