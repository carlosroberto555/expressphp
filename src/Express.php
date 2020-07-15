<?php

namespace ExpressPHP;

class Express extends Router
{
	// Application trait
	use Application;

	// Read-only props
	protected $_mountregexp, $_mountpath, $_mounturl;

	public function __construct($mountpath = '')
	{
		// Guarda as instâncias do router
		self::$instances[] = $this;

		if (empty($mountpath)) {
			$this->_mountpath = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
		} else {
			$this->_mountpath = $mountpath;
		}

		$this->_mountregexp = preg_replace('/(:\w+)/', '(\w+)', $this->_mountpath);
		$this->_mounturl = preg_replace('#(' . $this->_mountregexp . ').*#', '$1', $this->req->url);

		// Create app props
		$this->create_app($this->_mounturl);
	}

	public function __invoke($req, $res, $next)
	{
		$this->_mountregexp = $req->app->mountregexp;
		$this->_mountpath = $req->app->mountpath;
		$this->_mounturl = $req->app->mounturl;

		$this->req->baseUrl = $req->baseUrl;
		$next();
	}

	public static function require($file)
	{
		return function ($req, $res, $next) use ($file) {
			$next();
			require $file;
		};
	}

	public static function render($file)
	{
		return function ($req, $res, $next) use ($file) {
			include $file;
		};
	}

	public static function static($path, $options = [])
	{
		return function ($req, $res, $next) use ($path) {
			if (is_file($path . $req->path)) {
				$res->sendFile($path . $req->path);
			} else {
				$next();
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
