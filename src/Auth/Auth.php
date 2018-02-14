<?php
namespace ExpressPHP\Auth;

abstract class Auth implements \ExpressPHP\Router\RouterCallable {
	public $user;

	public abstract function get_user();
	public abstract function is_authenticated() : bool;
	public abstract function authenticate($user, $pass);
	public abstract function use_strategie($req, $res) : bool;

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

		// Passa a instância do usuário
		$this->user = $this->get_user();

		// Se essa estratégia estiver disponível
		if ($this->use_strategie($req, $res))
		{
			// Inicia a sessão
			$this->session_start();
	
			// Atribui o usuário
			$req->auth = $this;
			$req->user = &$this->user;
		}

		$next();
	}
}
