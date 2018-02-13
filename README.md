# ExpressPHP 

A express like lib for PHP language

## Installation

You need first to have *composer* previous installed. You can see more on [https://getcomposer.org](https://getcomposer.org).

Now you install this package using:

```bash
$ composer require carlosroberto555/expressphp
```

## Quick Start

*Under construction*

Basic use;

```php
<?php
include 'vendor/autoload.php';

$app = new ExpressPHP\Express;

$app->use('/', function ($req, $res) {
    $res->send('Hello world!');
});
```