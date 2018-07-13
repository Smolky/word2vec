<?php

namespace UMUWords;

use Symfony\Component\Finder\Finder;


/**
 * GUI
 *
 * This controllers handles the logic about the authentication
 * form of the application
 *
 * @package UMUWords
 */
class GUI extends \CoreOGraphy\BaseController {
    
    /**
     * handleRequest
     *
     * @package UMUWords
     */
    public function handleRequest () {
        
        /** @var $finder Finder */
        $finder = new Finder ();
        
        
        // Configure finder
        $finder->files ()->in (CORPUS_URL);
        
        
        /** @var $data Array */
        $data = [
            'corpora' => $finder
        ];
        
        
        // Render the template
        $this->_response->getBody ()->write ($this->_template->render ('gui.html.twig', $data));   
    }
}