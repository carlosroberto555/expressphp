<?php
namespace ExpressPHP;

class Express extends Router {
	
	// Application trait
	use Application;

	// Read-only props
	private $props = [
		'mountregexp' => '',
		'mountpath' => '',
		'mounturl' => ''
	];

	public function __construct($mountpath = '')
	{
		// Guarda as instâncias do router
		self::$instances[] = $this;

		// Instancia o Request e o response
		$this->req = new Router\Request;
		$this->res = new Router\Response;

		if (empty($mountpath)) {
			$this->props['mountpath'] = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
		} else {
			$this->props['mountpath'] = $mountpath;
		}

		$this->props['mountregexp'] = preg_replace('/(:\w+)/', '(\w+)', $this->props['mountpath']);
		$this->props['mounturl'] = preg_replace('#('.$this->props['mountregexp'].').*#', '$1', $this->req->url);

		$this->req->app = $this;
		$this->req->baseUrl = $this->mounturl;
	}

	public function __invoke($req, $res, $next)
	{
		$this->props['mountregexp'] = $req->app->mountregexp;
		$this->props['mountpath'] = $req->app->mountpath;
		$this->props['mounturl'] = $req->app->mounturl;

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

	public static function static($path, $options = []) {
		return function ($req, $res) use ($path) {
			$res->sendFile($path.$req->path);
		};
	}

	public static function Router() {

		$app = end(self::$instances);
		$router = new Express($app->req->baseUrl.$app->req->path);

		$router->req = clone $app->req;
		$router->res = clone $app->res;

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
