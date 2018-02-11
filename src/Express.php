<?php
namespace ExpressPHP;

class Express extends Router {
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
		$this->uri = preg_replace("#$this->home#", '', $_SERVER['REQUEST_URI']);

		$this->req = new Router\Request($this->uri);
		$this->res = new Router\Response($this->home);

		$this->req->app = $this;

		if (empty($mountpath)) {
			$this->props['mountpath'] = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
			$this->props['mountregexp'] = preg_replace('/(:\w+)/', '(\w+)', $this->props['mountpath']);
			$this->props['mounturl'] = preg_replace('#('.$this->props['mountregexp'].').*#', '$1', $this->req->url);
		}
	}

	public function __invoke($req, $res, $next)
	{
		$this->props['mountpath'] = $req->app->mountpath;
	}

	public function require($file)
	{
		return function ($req, $res, $next) use ($file) {
			// $router = $this->router;
			// $router->path = $this->path;
			require $file;
			$next();
		};
	}

	public function Router() {
		return new Express($this->path);
	}

	function trim_uri($uri) {
		return $uri !== '/' ? rtrim($uri, '/') : '/';
	}

	public function teste() : Express {
		return new Express;
}

	public function __get($name) {
		return $this->props[$name];
	}
}

// Express::$req = new Router\Request(Express::$uri);
// Express::$res = new Router\Response(Express::$home);
// Express::use(new Router(Express::$home));
