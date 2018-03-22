<?php
namespace ExpressPHP\Auth;

class User {

	protected $_anonymous = true;

	public function __get($name) {
		return $this->{"_$name"};
	}

	public function __set($name, $value) {
		if (isset($this->{"_$name"})) {
			throw new \Exception("O campo $name é de apenas leitura");
		}
	}
}