<?php

/**
 * Config file
 *
 * @package UMUWords
 */
 
/** @var $production boolean  Defines if the application is 
                         in production */
$production = false;


// Routes
/** @var $base_url String Defines the base_path of the application */
$base_url = '/word2vec/';


/** @constant corpus_url String Defines the temp_path of the application */
define ('CORPUS_URL', './tmp/corpus/');


/** @constant corpus_url String Defines the temp_path of the application */
define ('VECTOR_URL', './tmp/vectors/');
