<?php

namespace ExpressPHP\Router;

interface RouterCallback
{
	public function __invoke(Request $req, Response $res, callable $next);
}
