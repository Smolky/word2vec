<?php

namespace UMUWords;

use \Zend\Diactoros\Response\JsonResponse;


/**
 * CreateModel
 *
 * This controller allows to create new model in the system
 * based on a file
 *
 * @package UMUWords
 */
class CreateModel extends \CoreOGraphy\BaseController {
    
    /**
     * handleRequest
     *
     * @package UMUWords
     */
    public function handleRequest () {
        
        /** @var $filename String */
        $filename = filter_var ($_POST['filename'], FILTER_DEFAULT);
        
        
        /** @var $filename String */
        $raw_content = filter_var ($_POST['content'], FILTER_DEFAULT);
        
        
        /** @var $mime_type String */
        list ($mime_type, $content) = explode (',', $raw_content);
        
        
        /** @var $content String */
        $content = base64_decode ($content);
        
        
        // Create the corpus
        file_put_contents (CORPUS_PROCESSED_DIR . $filename, $content);
        
        
        // Return response
        $this->_response = new JsonResponse (['ok' => true]);
        $this->_response = $this->_response->withHeader ('Content-Type', 'application/json; charset=utf8');
        $this->_response = $this->_response->withEncodingOptions (JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    }
}