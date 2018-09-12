<?php

namespace UMUWords;

use \voku\helper\StopWords;
use \Symfony\Component\Process\Process;
use \Symfony\Component\Process\Exception\ProcessFailedException;
use \Zend\Diactoros\Response\JsonResponse;


/**
 * Distance
 *
 * @package UMUWords
 */
class Distance extends \CoreOGraphy\BaseController {
    
    /**
     * handleRequest
     *
     * @package UMUWords
     */
    public function handleRequest () {
    
        /** @var $stop_words_provider StopWords */
        $stop_words_provider = new StopWords ();
        
        
        /** @var $stop_words Array */
        $stop_words = $stop_words_provider->getStopWordsFromLanguage ('es');

        
        /** @var $word String */
        $word = mb_strtolower (filter_input (INPUT_GET, 'word', FILTER_SANITIZE_STRING));
        
        
        /** @var $vector_file String */
        $vector_file = filter_input (INPUT_GET, 'model', FILTER_SANITIZE_STRING);
        
        
        /** @var $vector_file_path String The vector file */
        $vector_file_path = VECTOR_URL . $vector_file;        
        
        
        /** @var command String */
        $command = 
            './vendor/google-word2vec-trunk/distance ' . $vector_file_path . ' ' . $word;
        ;


        /** @var process Process */
        $process = new Process ($command);
        $process->run ();
        
        
        /** @var process Process */
        $output = $process->getOutput();
        
        
        /** @var $process Array */
        $lines = explode ("\n", $output);
        $lines = array_filter ($lines);
        
        
        
        /** @var response Array */
        $response = array_map (function ($line) use ($stop_words) { 
            
            /** @var $parts Array */
            $parts = explode (',', $line);
            
            
            /** @var $word String */
            $word = reset ($parts);

            
            // No keywords
            if ($word === '{num}') {
                return null;
            }
            
            
            // Remove stop-words
            if (in_array ($word, $stop_words)) {
                return null;
            }

        
            return [reset($parts), end ($parts)];
        }, $lines);
        
        
        $response = array_filter ($response);
        $response = array_slice ($response, 0, 60);
        
        
        $this->_response = new JsonResponse (['rows' => $response]);
        $this->_response = $this->_response->withHeader ('Content-Type', 'application/json; charset=utf8');
        $this->_response = $this->_response->withEncodingOptions (JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    }
}
