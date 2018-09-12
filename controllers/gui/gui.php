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
        
        /** @var $CorporaFinder Finder */
        $CorporaFinder = new Finder ();
        
        
        /** @var $ModelFinder Finder */
        $ModelFinder = new Finder ();
        
        
        // Configure finders
        $CorporaFinder->files ()->in (CORPUS_URL);
        $ModelFinder->files ()->in (VECTOR_URL);
        
        
        /** @var $data Array */
        $data = [
            'corpora' => $CorporaFinder,
            'models'  => $ModelFinder
        ];
        
        
        // Render the template
        $this->_response->getBody ()->write ($this->_template->render ('gui.html.twig', $data));   
    }
}