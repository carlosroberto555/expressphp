# ExpressPHP

A express like lib for PHP language

## Installation

You need first to have _composer_ previous installed. You can see more on [https://getcomposer.org](https://getcomposer.org).

Now you install this package using:

```bash
$ composer require carlosroberto555/expressphp
```

## Quick Start

Basic use;

```php
<?php
include 'vendor/autoload.php';

$app = new ExpressPHP\Express;

$app->use('/', function ($req, $res) {
    $res->send('Hello world!');
});
```

### Include a children app scope

The new app that includes a children route. The Express app has a static method require, to include a php executable file with actual route scope.

<!-- This app has access to route props `$req`, `$res`, `$next`. -->

```php
<?php
include 'vendor/autoload.php';

$app = new ExpressPHP\Express;

$app->use('/api', app::require('/routes/api.php'));
```

Children route:

```php
<?php
// file /routes/api.php
$router = ExpressPHP\Express::Router();

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

```

### Include a static content children route

Express has a method called static to include static content. This just send files like images, html, css, js with a cache control header `I-Modiffied-Since`.

```php
<?php
include 'vendor/autoload.php';

$app = new ExpressPHP\Express;

$app->use('/hello', app::static('/static/index.html'));
// $app->use('/css', app::static('/static/css'));
// $app->use('/uploads', app::static('/static/images/uploads'));
```

The html example file:

```html
<!DOCTYPE html>
<!-- /static/index.html -->
<html>
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <h1>Hello world</h1>
  </body>
</html>
```
