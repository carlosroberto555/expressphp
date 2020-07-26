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
