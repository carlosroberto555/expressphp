<?php
namespace ExpressPHP;

trait Application {
	protected static $instances = [];
	protected $req, $res;
}