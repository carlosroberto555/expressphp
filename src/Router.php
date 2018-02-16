<?php
namespace ExpressPHP;

class Router {

	public function get(string $path, callable ...$callbacks) {
		$this->request($path, 'GET', ...$callbacks);
	}

	public function put(string $path, callable ...$callbacks) {
		$this->request($path, 'PUT', ...$callbacks);
	}

	public function post(string $path, callable ...$callbacks) {
		$this->request($path, 'POST', ...$callbacks);
	}

	public function delete(string $path, callable ...$callbacks) {
		$this->request($path, 'DELETE', ...$callbacks);
	}

	public function all(string $path, ...$callbacks) {
		$this->request($path, '*', ...$callbacks);
	}

	public function request(string $path, string $method, callable ...$callbacks)
	{
		if ($method === '*' || preg_match("/$method/", $this->req->method)) {

			$url = str_replace($this->req->baseUrl, '', $this->req->url);
			$route = new Router\Route($this->req->baseUrl, $path);
			
			// Verifica se a rota bate
			if ($route->matches($url, true)) {

				$this->req->route = $route;
				$this->req->path = $route->result_path;
				$this->req->params = $route->params;
				$this->req->baseUrl = $this->req->app->mounturl;

				$this->execute_callbacks($callbacks);
			}
		}
	}

	public function use($path, callable ...$callbacks) {

		// Se passar uma função sem path
		if (is_callable($path)) {
			array_unshift($callbacks, $path);
			$path = '/';
		}

		$url = str_replace($this->req->app->mounturl, '', $this->req->url);
		$route = new Router\Route($this->req->app->mounturl, $path);

		// Verifica se a rota bate
		if ($route->matches($url)) {

			$this->req->route = $route;
			$this->req->path = $route->result_path;
			$this->req->params = $route->params;
			$this->req->baseUrl = $this->req->app->mounturl.$route->result_baseUrl;

			$this->execute_callbacks($callbacks);
		}
	}

	public function execute_callbacks(array $callbacks)
	{
		// Se não tiver next, encerra
		static $next = true;

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
