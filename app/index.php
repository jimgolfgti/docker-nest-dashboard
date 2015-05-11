<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/fetch', function ($name) use ($app) {

});

$app->run();
