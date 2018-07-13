<?php

namespace UMUWords;

use \Symfony\Component\Process\Process;
use \Symfony\Component\Process\Exception\ProcessFailedException;
use \Symfony\Component\Finder\Finder;
use \voku\helper\StopWords;
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
        
        
        /** @var $blacklist_characters Array */
        $blacklist_characters = [
            "'", "‘", "\"", "“", 
            ",", ".", "*", 
            "!", "¡", "¿", "?", 
            "— ", "—", "-", "»", 
            "#", ":", ";"
        ];            
        
        
        
        /** @var $stop_words Array */
        $stop_words = $stop_words_provider->getStopWordsFromLanguage ('es');
        
        
        /** @var $finder Finder */
        $finder = new Finder ();
        
        
        /** @var $word String */
        $word = mb_strtolower (filter_input (INPUT_GET, 'word', FILTER_SANITIZE_STRING));
        $word = str_replace ($blacklist_characters, "", $word);
        
        
        /** @var $corpus String */
        $corpus = filter_input (INPUT_GET, 'corpus', FILTER_SANITIZE_STRING);
        
        
        /** @var $vector_file String The vector file */
        $vector_file = str_replace ('.txt', '.bin', $corpus);
        
        
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
        
        
        // Remove multiple blank lines
        $plain_corpus = preg_replace ('/\n{2,}/', ' ', $plain_corpus);
        
        
        // Remove soft break
        $plain_corpus = preg_replace ('/\r\n(\w+)/im', '${1}', $plain_corpus);
        
        
        // Remove spaces after a new line
        $plain_corpus = preg_replace ('/\n(\s+)/im', "\n", $plain_corpus);
        
        
        // Remove special characters
        $plain_corpus = str_replace ($blacklist_characters, "", $plain_corpus);
        
        
        // Get train file
        file_put_contents (CORPUS_PROCESSED_DIR . $corpus, $plain_corpus);
        
        
        
        /** @var $train_file String */
        $train_file = CORPUS_PROCESSED_DIR . $corpus;
        
        
        /** @var command String */
        $command = 
            './vendor/google-word2vec-trunk/word2vec ' 
            . ' -train ' . $train_file 
            . ' -output ' . $vector_file_path 
            . ' -cbow 1'
            . ' -size 200' 
            . ' -window 8 '
            . ' -negative 25'
            . ' -hs 0'
            . ' -sample 1e-4'
            . ' -threads 20'
            . ' -binary 1'
            . ' -iter 15'
        ;

        /** @var process Process */
        $process = new Process ($command);
        $process->run ();
        
        
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
        
        
            // No stop words
            if (in_array ($word, $stop_words)) {
                return null;
            }
            
            
            // No keywords
            if ($word === '{num}') {
                return null;
            }
            
        
            return [reset($parts), end ($parts)];
        }, $lines);
        
        
        $response = array_filter ($response);
        $response = array_slice ($response, 0, 20);
        
        
        $this->_response = new JsonResponse (['rows' => $response]);
        $this->_response = $this->_response->withHeader ('Content-Type', 'application/json; charset=utf8');
        $this->_response = $this->_response->withEncodingOptions (JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    }
}
