<?php

// Routes
$router->map ('GET', '/', function () {
    require __DIR__ . '/controllers/gui/gui.php';
    return new \UMUWords\GUI ();
});

$router->map ('GET', '/distance', function () {
    require __DIR__ . '/controllers/word2vec/Distance.php';
    return new \UMUWords\Distance ();
});

$router->map ('POST', '/create-corpus', function () {
    require __DIR__ . '/controllers/gui/CreateCorpus.php';
    return new \UMUWords\CreateCorpus ();
});

$router->map ('POST', '/create-model-from-corpus', function () {
    require __DIR__ . '/controllers/gui/CreateModelFromCorpus.php';
    return new \UMUWords\CreateModelFromCorpus ();
});




