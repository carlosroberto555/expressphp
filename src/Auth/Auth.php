<?php
namespace ExpressPHP\Auth;

abstract class Auth implements \ExpressPHP\Router\RouterCallable {
	public $user;

	public abstract function get_user();
	public abstract function is_authenticated() : bool;
	public abstract function authenticate($user, $pass);

	public function session_start() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	public function logout() {
		session_destroy();
	}

	/**
	 * Midleware
	 */
	public function __invoke($req, $res, $next) {

		$this->session_start();

		$req->auth = $this;
		$req->user = &$this->user;

		$next();
	}
}
