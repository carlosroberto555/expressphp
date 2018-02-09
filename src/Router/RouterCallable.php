<?php
namespace ExpressPHP\Router;

interface RouterCallable {
	public function __invoke($req, $res, $next);
}