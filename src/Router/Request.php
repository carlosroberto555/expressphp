<?php
namespace ExpressPHP\Router;

class Request {
	public $body;
	public $query;
	public $params;

	public $url;
	public $baseUrl;
	public $originalUrl;
	public $method;
	public $path;

	function __construct() {

		$this->url = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->originalUrl = $_SERVER['REQUEST_URI'];

		$this->query = (object) $_GET;

		if ($this->type('application/json')) {
			$this->body = json_decode(file_get_contents('php://input'));
		} else {
			$this->body = (object) $_POST;
		}
	}

	public function type($value) {
		return isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] === $value : false;
	}

	public function query ($key, $default = null) {
		return isset($this->query->$key) ? $this->query->$key : $default;
	}

	public function body ($key) {
		return isset($this->body->$key) ? $this->body->$key : null;
	}
}
