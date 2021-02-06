<?php

namespace ExpressPHP;

use ExpressPHP\Router;
use ExpressPHP\Router\{Request, Response};

class Express extends Router
{
  // Application trait
  use Application;

  /**
   * ```php
   * // Example:
   * require '../vendor/autoload.php';
   * // Listen route /api/users
   * // Files path is /project/users
   * use ExpressPHP\Express as app;
   * $app = new app('/api/users');
   * ```
   * @param string $mountpath App path to listen on browser url
   */
  public function __construct(string $mountpath = '')
  {
    // Create app props
    $this->create_app($mountpath);
  }

  public function __invoke(Request $req, Response $res, $next)
  {
    $this->_mountregexp = $req->app->mountregexp;
    $this->_mountpath = $req->app->mountpath;
    $this->_mounturl = $req->app->mounturl;

    $this->req->baseUrl = $req->baseUrl;
    $next();
  }

  public static function require($file)
  {
    return function (Request $req, Response $res, $next) use ($file) {
      $next();
      require $file;
    };
  }

  public static function render($file)
  {
    return function (Request $req, Response $res, $next) use ($file) {
      include $file;
    };
  }

  public static function static($path, $options = [])
  {
    return function (Request $req, Response $res, $next) use ($path) {
      $file = join('', [$path, $req->path]);

      if (is_file($file . '/index.html')) {
        $res->sendFile($file . '/index.html');
      } else if (is_file($file)) {
        $res->sendFile($file);
      } else {
        $res->status(404);
        $res->end('Not found');
      }
    };
  }

  public static function Router()
  {
    return self::create_router();
  }

  function trim_uri($uri)
  {
    return $uri !== '/' ? rtrim($uri, '/') : '/';
  }

  public function __get($name)
  {
    return $this->{"_$name"};
  }

  public function __set($name, $value)
  {
    if (isset($this->{"_$name"})) {
      throw new \Exception("O campo $name é de apenas leitura");
    }
  }

  public function isset($name)
  {
    return isset($this->{"_$name"});
  }
}
