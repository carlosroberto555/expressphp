<?php

use ExpressPHP\Express as app;

$router = app::Router();

// GET /api/users
$router->get('/users', function ($req, $res) {
  $res->json([
    ['name' => 'Libbie Dunn'],
    ['name' => 'Ella-Mai Davies'],
    ['name' => 'Elsie-Rose Dennis'],
    ['name' => 'Zena Slater'],
    ['name' => 'Antoni Partridge'],
  ]);
});

// GET /api/users/:id/uploads
// Use any logic to change path
// File path is static/1/test.txt
$router->use('/users/:id/uploads', function ($req, $res, $next) {
  // Nested app static response needs to call returns closure with params
  app::static('static/uploads/' . $req->params->id)($req, $res, $next);
});
