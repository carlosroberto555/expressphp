<?php

namespace ExpressPHP\Auth;

use ExpressPHP\Router\{Request, Response, RouterCallback};

abstract class Auth implements RouterCallback
{
  public $user;
  public $session_name;

  public function __construct($session_name = null)
  {
    $this->session_name = $session_name;
  }

  public abstract function get_user();
  public abstract function set_user($user);

  public abstract function is_authenticated(): bool;
  public abstract function authenticate($user, $pass);
  public abstract function use_strategie($req, $res): bool;

  public function session_start($options = [])
  {
    if (session_status() == PHP_SESSION_NONE) {
      //session_name($this->session_name);
      session_start($options);
    }
  }

  public function logout()
  {
    session_destroy();
  }

  /**
   * Midleware
   */
  public function __invoke(Request $req, Response $res, $next)
  {

    // Inicia a sessão
    $this->session_start();

    // Passa a instância do usuário
    $this->user = $this->get_user();
    $req->user = &$this->user;

    // Se essa estratégia estiver disponível
    if ($this->use_strategie($req, $res)) {
      $req->auth = $this; // Atribui o usuário
    }

    $next();
  }
}
