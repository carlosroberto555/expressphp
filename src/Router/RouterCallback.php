<?php

namespace ExpressPHP\Router;

interface RouterCallback extends \Closure
{
	public function __invoke(Request $req, Response $res, callable $next);
}
