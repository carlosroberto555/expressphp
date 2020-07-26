<?php
require '../vendor/autoload.php';

use ExpressPHP\Express as app;

$app = new app('/projects/expressphp');

// Show a hello world
// GET /
$app->get('/', function ($req, $res) {
  $res->send('Hello world!');
});

// Short phpinfo with arrow function (php 7.4)
// ALL_METHODS /info
$app->all('/info',  fn () => phpinfo());

$app->use('/api', app::require('routes/api.php'));
