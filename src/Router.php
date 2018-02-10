<?php
namespace ExpressPHP;

class Router {

	public function get($path, callable ...$callbacks) {
		$this->request($path, 'GET', ...$callbacks);
	}

	public function put($path, callable ...$callbacks) {
		$this->request($path, 'PUT', ...$callbacks);
	}

	public function post($path, callable ...$callbacks) {
		$this->request($path, 'POST', ...$callbacks);
	}

	public function delete($path, callable ...$callbacks) {
		$this->request($path, 'DELETE', ...$callbacks);
	}

	public function all($path, ...$callbacks)
	{
		$this->use($path.'$', ...$callbacks);
	}

	public function request($path, $method, ...$callbacks)
	{
		if ($_SERVER['REQUEST_METHOD'] == $method) {
			$this->all($path, ...$callbacks);
		}
	}

	public function use($path, callable ...$callbacks) {

		// Se não tiver next, encerra
		static $next = true;
		
		// Se passar uma função sem path
		if (is_callable($path)) {
			array_unshift($callbacks, $path);
			$path = '/';
		}

		$regex = $this->route_regex($path);
		
		// Verifica se a rota bate
		if ($this->route_matches($regex, $match)) {

			$this->req->baseUrl = $this->req->app->mountpath . $match;

			// Pega o path atual
			$this->req->path = $path;

			// Executa os callbacks
			foreach ($callbacks as $callback) {

				if (!$next) break;
				$next = false;

				$callback($this->req, $this->res, function () use (&$next) {
					$next = true;
				});
			}
		}
	}

	private function matches($uri) {
		$regex = $this->route_regex($uri);
		return $this->route_matches($regex);
	}

	/**
	 * Gera o regex para a rota atual
	 */
	private function route_regex($uri, $exact = false)
	{
		$uri = preg_replace('/(:\w+)/', '(\w+)', $uri);

		if ($exact) {
			return "#^($uri)/?$#";
		} else {
			return "#($uri)/?#";
		}
	}

	/**
	 * Verifica se a rota bate com a uri atual
	 */
	private function route_matches($regex, &$match)
	{
		preg_match($regex, $this->req->originalUrl, $matches);

		print_r($matches);

		if (!empty($matches)) {
			$match = $matches[0];
			return true;
		}

		return false;
	}
}