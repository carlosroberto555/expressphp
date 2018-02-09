<?php
namespace ExpressPHP\Router;

class Request {
	public $body;
	public $query;
	public $uri;
	public $params;
	public $local;

	function __construct($uri = '/') {

		$this->uri = $uri;
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

	public function query ($key) {
		return isset($this->query->$key) ? $this->query->$key : null;
	}

	public function body ($key) {
		return isset($this->body->$key) ? $this->body->$key : null;
	}
}