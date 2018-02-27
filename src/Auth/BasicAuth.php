<?php
namespace ExpressPHP\Auth;

abstract class BasicAuth extends Auth {

	public function use_strategie($req, $res) : bool {
		return isset($_SERVER['PHP_AUTH_USER']);
	}

	public function is_authenticated() : bool {

		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];
	
		if (!empty($this->user)) {
			return true;
		} else if ($this->authenticate($user, $pass)) {
			return true;
		} else {
			header('WWW-Authenticate: Basic');
			http_response_code(401);
			return false;
		}
	}
}