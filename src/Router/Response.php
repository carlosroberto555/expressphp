<?php
namespace ExpressPHP\Router;

class Response {

	private $home;

	public function __construct($home = '') {
		$this->home = $home;
	}

	public function send($resp) {
		echo $resp;
	}

	public function json($resp) {
		$this->type('application/json');

		if (DEBUG) {
			echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		} else {
			echo json_encode($resp);
		}
	}

	public function status($status) {
		http_response_code($status);
	}

	public function header($key, $value) {
		header("$key: $value");
	}

	public function location($loc) {
		$loc = $this->home . $loc;
		$this->header('Location', $loc);
	}

	public function type($type) {
		$this->header('Content-Type', "$type; charset=utf-8");
	}

	public function end() {
		exit;
	}
}