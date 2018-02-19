<?php
namespace ExpressPHP;

class Express extends Router {
	
	// Application trait
	use Application;

	// Read-only props
	protected $_mountregexp, $_mountpath, $_mounturl;

	public function __construct($mountpath = '')
	{
		// Guarda as instâncias do router
		self::$instances[] = $this;

		// Instancia o Request e o response
		$this->req = new Router\Request;
		$this->res = new Router\Response;

		if (empty($mountpath)) {
			$this->_mountpath = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
		} else {
			$this->_mountpath = $mountpath;
		}

		$this->_mountregexp = preg_replace('/(:\w+)/', '(\w+)', $this->_mountpath);
		$this->_mounturl = preg_replace('#('.$this->_mountregexp.').*#', '$1', $this->req->url);

		$this->req->app = $this;
		$this->req->baseUrl = $this->mounturl;
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
		return $this->{"_$name"};
	}

	public function __set($name, $value) {
		throw new \Exception('Campo de apenas leitura');
	}

	public function isset($name)
	{
		return isset($this->{"_$name"});
	}
}
