<?php
namespace ExpressPHP;

class Express extends Router {
	
	private static $instances = [];

	private $props = [
		'mountregexp' => '',
		'mountpath' => '',
		'mounturl' => ''
	];

	public $req;
	public $res;

	public function __construct($mountpath = '')
	{
		self::$instances[] = $this;

		$this->req = new Router\Request;
		$this->res = new Router\Response;

		if (empty($mountpath)) {
			$this->props['mountpath'] = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
			$this->props['mountregexp'] = preg_replace('/(:\w+)/', '(\w+)', $this->props['mountpath']);
			$this->props['mounturl'] = preg_replace('#('.$this->props['mountregexp'].').*#', '$1', $this->req->url);
		}

		$this->req->app = $this;
		$this->req->baseUrl = $this->mounturl;
	}

	public function __invoke($req, $res, $next) {
		$this->props['mountregexp'] = $req->app->mountregexp;
		$this->props['mountpath'] = $req->app->mountpath;
		$this->props['mounturl'] = $req->app->mounturl;
	}

	public static function require($file)
	{
		return function ($req, $res, $next) use ($file) {
			$next();
			require $file;
		};
	}

	public static function static($path, $options = []) {
		return function ($req, $res) use ($path) {
			$res->sendFile($path.$req->path);
		};
	}

	public static function Router() {

		$app = end(self::$instances);
		$router = new Router();

		$router->req = $app->req;
		$router->res = $app->res;
		$router->mounturl = $app->req->baseUrl;
		self::$instances[] = $router;

		return $router;
	}

	function trim_uri($uri) {
		return $uri !== '/' ? rtrim($uri, '/') : '/';
	}

	public function __get($name) {
		return $this->props[$name];
	}

	public function __set($name, $value) {
		throw new \Exception('Campo de apenas leitura');
	}
}
