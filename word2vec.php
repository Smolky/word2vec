<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Word2Vec
 *
 * @package Word2Vec GUI
 */
 
// Configure server
set_time_limit (0);
ini_set ('memory_limit', '-1');


// Require autload
require ('vendor/autoload.php');


/** @var train_file String */
$train_file = './tmp/quijote.txt';


/** @var output_file String */
$output_file = './tmp/vectors.bin';


/** @var command String */
$command = 
    './vendor/google-word2vec-trunk/word2vec ' 
    . ' -train ' . $train_file 
    . ' -output ' . $output_file 
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

$process = new Process ($command);
$process->start();


foreach ($process as $type => $data) {
    if ($process::OUT === $type) {
        echo "\nRead from stdout: ".$data;
    } else { // $process::ERR === $type
        echo "\nRead from stderr: ".$data;
    }
}

