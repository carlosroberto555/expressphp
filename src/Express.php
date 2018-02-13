<?php
namespace ExpressPHP;

class Express extends Router {
	
	private static $instances = [];

	private $props = [
		'mountregexp' => '',
		'mountpath' => '',
		'mounturl' => ''
	];

	public $router;
	public $req;
	public $res;

	public $path = '/';
	public $home;
	public $uri;

	public function __construct($mountpath = '')
	{
		self::$instances[] = $this;
		$this->uri = preg_replace("#$this->home#", '', $_SERVER['REQUEST_URI']);

		$this->req = new Router\Request($this->uri);
		$this->res = new Router\Response($this->home);

		if (empty($mountpath)) {
			$this->props['mountpath'] = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
			$this->props['mountregexp'] = preg_replace('/(:\w+)/', '(\w+)', $this->props['mountpath']);
			$this->props['mounturl'] = preg_replace('#('.$this->props['mountregexp'].').*#', '$1', $this->req->url);
		}

		$this->req->app = $this;
		$this->req->baseUrl = $this->mounturl;
	}

	public function __invoke($req, $res, $next)
	{
		$this->props['mountpath'] = $req->app->mountpath;
	}

	public static function require($file)
	{
		return function ($req, $res, $next) use ($file) {
			require $file;
			$next();
		};
	}

	public static function static($path, $type='*/*') {
		return function ($req, $res, $next) use ($path, $type) {
			$res->type($type);
			readfile($path.$req->path);
			$res->end();
		};
	}

	public static function Router() {

		$app = end(self::$instances);
		$router = new Router();

		$router->req = $app->req;
		$router->res = $app->res;
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

// Express::$req = new Router\Request(Express::$uri);
// Express::$res = new Router\Response(Express::$home);
// Express::use(new Router(Express::$home));
