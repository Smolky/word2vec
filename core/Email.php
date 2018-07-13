<?php

namespace CoreOGraphy;

use \Pelago\Emogrifier;
use \Hampe\Inky\Inky;

/**
 * Email
 *
 * This class creates an email from a template
 *
 * @package Allergic
 */
 
class Email {

    /** $_to String Receiver */
    protected $_to;
    
    
    /** $_template String the name of the template */
    protected $_template;
    
    
    /** $_params Array */
    protected $_params = array ();
    
    
    /** $_base_css String */
    private $_base_css;
    
    
    /** $_base_template Template */
    private $_base_template;
    
    
    /** $_transport Transport */
    private $_transport;
    
    
    /** $_twig Twig */
    private $_twig;
    
    
    /** $_inky Inky */
    private $_inky;
    
    
    /** $_emogrifier Emogrifier */
    private $_emogrifier;
    
    
    /**
     * __construct
     *
     * @param $template String The template name
     * @param $params Array|null 
     *
     * @package Allergic
     */
    function __construct ($template, $params = array ()) {
    
        // Get the dependency container
        global $container;
    
    
        // Configure
        $this->setTemplate ($template);
        $this->setParams ($params);
    
    
        // Create Inky
        $this->_inky = new Inky (12, []);
        
        
        // Create emogrifier
        $this->_emogrifier = new Emogrifier ();
        $this->_emogrifier->disableStyleBlocksParsing ();
        
        
        // Create transport layer
        $this->_transport = $container['transport'];
        
        
        // Get TWIG
        $this->_twig = $container['templates'];

    
    }
    
        
    /**
     * setTemplate
     *
     * @param $template String
     *
     * @package Allergic
     */
    public function setTemplate ($template) {
        $this->_template = $template;
    }
    
    
    /**
     * getParams
     *
     * @return Array
     *
     * @package Allergic
     */
    public function getParams () {
        return $this->_params;
    }
    
    
    /**
     * setParams
     *
     * @param $params Array
     *
     * @package Allergic
     */
    public function setParams ($params) {
        $this->_params = $params;
    }    
    

    
    /**
     * setTo
     *
     * @param $to String
     *
     * @package Allergic
     */
    public function setTo ($to) {
        $this->_to = $to;
    }
    
    
    /**
     * setBaseCSS
     *
     * @package Allergic
     */    
    public function setBaseCSS () {
        if ( ! $this->_base_css) {
            $this->_base_css = file_get_contents ('./css/foundation-emails.css');
        }
    }
    
    
    /**
     * setBaseTemplate
     *
     * @package Allergic
     */    
    public function setBaseTemplate () {
        if ( ! $this->_base_template) {
            $this->_base_template = $this->_twig->load ($this->_template);
        }
    }    

    
    /**
     * send
     *
     * @package Allergic
     */
    public function send () {
    
        // Obtain the foundation CSS
        $this->setBaseCSS ();
        
        
        // Get template
        $this->setBaseTemplate ();
        
        
        // Get base HTML of the template
        $html = $this->_base_template->render ($this->getParams ());
        
        
        // Convert INKY to HTML
        $html = $this->_inky->releaseTheKraken ($html);
        
        
        // Apply inline styles
        $this->_emogrifier->setHtml ($html);
        $this->_emogrifier->setCss ($this->_base_css);
        $html = $this->_emogrifier->emogrify();
        
        
        // Get decoration
        $decoration_template = $this->_twig->load ('email.html');
        $html = $decoration_template->render ([
            'foundation_css' => $this->_base_css,
            'html' => $html
        ]);
        
        
        // Send email to the user
        $message = Swift_Message::newInstance()
            ->setSubject ('UMUAllergic')
            ->setFrom (array ('joseantonio.garcia8@um.es' => 'joseantonio.garcia8@um.es'))
            ->setTo (array ($this->_to))
            ->setBody ($html, 'text/html')
        ;
        
        
        // Set the transport layer
        $mailer = Swift_Mailer::newInstance ($this->_transport);
        
        
        // Send the message
        $mailer->send ($message);
        
    }
}
