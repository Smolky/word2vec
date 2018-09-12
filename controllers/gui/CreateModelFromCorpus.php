<?php

namespace UMUWords;

use \Zend\Diactoros\Response\JsonResponse;
use \Symfony\Component\Process\Process;
use \Symfony\Component\Process\Exception\ProcessFailedException;


/**
 * CreateModelFromCorpus
 *
 * This controllers allos the creation of a model 
 * from a corpus file
 *
 * @package UMUWords
 */
class CreateModelFromCorpus extends \CoreOGraphy\BaseController {
    
    /**
     * handleRequest
     *
     * @package UMUWords
     */
    public function handleRequest () {
        
        /** @var $blacklist_characters Array */
        $blacklist_characters = [
            "'", "‘", "\"", "“", 
            ",", ".", "*", 
            "!", "¡", "¿", "?", 
            "— ", "—", "-", "»", 
            "#", ":", ";", "•"
        ];
        
        
        /** @var $corpus String */
        $corpus = filter_input (INPUT_POST, 'corpus', FILTER_SANITIZE_STRING);
        
        
        /** @var $window_size int */
        $window_size  = filter_input (INPUT_POST, 'window_size', FILTER_SANITIZE_NUMBER_INT);
        
        
        /** @var $size int */
        $size  = filter_input (INPUT_POST, 'size', FILTER_SANITIZE_NUMBER_INT);
        
        
        /** @var $iterations int */
        $iterations  = filter_input (INPUT_POST, 'iterations', FILTER_SANITIZE_NUMBER_INT);
        
        
        
        /** @var $vector_file String The vector file */
        $vector_file = str_replace ('.txt', '.bin', $corpus . '-' . $window_size . '-' . $size . '-' . $iterations);


        /** @var $vector_file_path String The vector file */
        $vector_file_path = VECTOR_URL . $vector_file;
        
        
        /** @var $plain_corpus String The corpus file */
        $plain_corpus = file_get_contents (CORPUS_URL . $corpus);
        
        
        // Convert to lowercase
        $plain_corpus = mb_strtolower ($plain_corpus);
        
        
        // Digits to special character
        $plain_corpus = preg_replace ('/\d+/', '{num}', $plain_corpus);
        
        
        // Remove URLs
        $plain_corpus = preg_replace ('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '{url}', $plain_corpus);
        
        
        // Remove tabs
        $plain_corpus = preg_replace ('/\t/', '', $plain_corpus);
        
        
        // Remove multiple blank lines
        $plain_corpus = preg_replace ('/\n{2,}/', "\n", $plain_corpus);
        
        
        // Remove soft break
        $plain_corpus = preg_replace ('/\r\n(\w+)/im', '${1}', $plain_corpus);
        
        
        // Remove spaces after a new line
        $plain_corpus = preg_replace ('/\n(\s+)/im', "\n", $plain_corpus);
        
        
        // Remove special characters
        $plain_corpus = str_replace ($blacklist_characters, " {signo} ", $plain_corpus);        
        
        
        // Store the processed corpus
        file_put_contents (CORPUS_PROCESSED_DIR . $corpus, $plain_corpus);
        
        
        /** @var $train_file String */
        $train_file = CORPUS_PROCESSED_DIR . $corpus;
        
        
        /** 
         * @var command String 
         *
         * https://medium.freecodecamp.org/how-to-get-started-with-word2vec-and-then-how-to-make-it-work-d0a2fca9dad3
         */
        
        $command = './vendor/google-word2vec-trunk/word2vec ' 
        
            // Train file
            . ' -train ' . $train_file 
            
            // Output file
            . ' -output ' . $vector_file_path 
            
            // architecture: skip-gram (slower, better for infrequent words) vs CBOW (fast)
            . ' -cbow 1'
            
            // dimensionality of the word vectors: usually more is better, but not always
            . ' -size ' . $size
            
            // context (window) size: for skip-gram usually around 10, for CBOW around 5
            . ' -window ' . $window_size
            . ' -negative 25'
            . ' -hs 0'
            
            // sub-sampling of frequent words
            // can improve both accuracy and speed for large data sets 
            // (useful values are in range 1e-3 to 1e-5)
            . ' -sample 1e-4'
            
            // For performance
            . ' -threads 20'
            
            // Used a binary source
            . ' -binary 1'
            
            // Number of iterations
            . ' -iter ' . $iterations
        ;
        
        
        /** @var process Process */
        $process = new Process ($command);
        $process->run ();        
        
        
        // Return response
        $this->_response = new JsonResponse (['ok' => true]);
        $this->_response = $this->_response->withHeader ('Content-Type', 'application/json; charset=utf8');
        $this->_response = $this->_response->withEncodingOptions (JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);        
        
    }
}