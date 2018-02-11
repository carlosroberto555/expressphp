<?php
namespace ExpressPHP\Auth;

abstract class BasicAuth extends Auth {

	public function get_user() {
		return isset($_SESSION['user']) ? (object) $_SESSION['user'] : null;
	}

	public function is_authenticated() : bool {

		if (!empty($this->user) || isset($_SERVER['PHP_AUTH_USER']))
		{
			$user = $_SERVER['PHP_AUTH_USER'];
			$pass = $_SERVER['PHP_AUTH_PW'];

			if ($this->authenticate($user, $pass)) {
				return true;
			} else {
				http_response_code(401);
				return false;
			}
		}
		else
		{
			header('WWW-Authenticate: Basic');
			http_response_code(401);
			return false;
		}
	}
}