<?php
namespace ExpressPHP;

class Express {
	public static $router;
	public static $req;
	public static $res;
	private static $next = true;

	public static $home;
	public static $uri;

	public static function require($file)
	{
		return function ($req, $res, $next) use ($file) {
			$router = self::$router;
			$router->path = $req->local->path;
			require $file;
			$next();
		};
	}

	public static function get($path, callable ...$callbacks) {
		self::request($path, 'GET', ...$callbacks);
	}

	public static function all($path, ...$callbacks)
	{
		self::use($path.'$', ...$callbacks);
	}

	public static function request($path, $method, ...$callbacks)
	{
		if ($_SERVER['REQUEST_METHOD'] == $method) {
			self::all($path, ...$callbacks);
		} else {
			self::$next = true;
		}
	}

	public static function use($path, callable ...$callbacks) {

		// Se não tiver next, encerra
		if (!self::$next) return;
		
		// Se passar uma função sem path
		if (is_callable($path)) {
			array_unshift($callbacks, $path);
			$path = '/';
		}
		
		// Verifica se a rota bate
		if (preg_match("#$path#", $_SERVER['REQUEST_URI'])) {

			// Pega o path atual
			self::$req->local = (object) ['path' => $path];

			// Executa os callbacks
			foreach ($callbacks as $callback) {

				if (!self::$next) break;
				self::$next = false;

				$callback(self::$req, self::$res, function () {
					self::$next = true;
				});
			}
		}
	}

	public static function Router() {
		return self::$router;
	}

	private function matches($uri) {
		$regex = $this->route_regex($uri);
		return $this->route_matches($regex);
	}

	/**
	 * Gera o regex para a rota atual
	 */
	private function route_regex($uri) {
		$uri = preg_replace('/(:\w+)/', '(\w+)', $uri);
		return "#^$uri/?$#" ;
	}

	/**
	 * Verifica se a rota bate com a uri atual
	 */
	private function route_matches($regex) {
		// return preg_match($regex, $this->uri);
	}

	static function trim_uri($uri) {
		return $uri !== '/' ? rtrim($uri, '/') : '/';
	}
}

Express::$home = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
Express::$uri = preg_replace('#'.Express::$home.'#', '', $_SERVER['REQUEST_URI']);

Express::$req = new Router\Request(Express::$uri);
Express::$res = new Router\Response(Express::$home);
Express::use(new Router(Express::$home));
