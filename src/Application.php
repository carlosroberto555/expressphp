<?php

namespace ExpressPHP;

use ExpressPHP\Router\{Request, Response};

trait Application
{
	protected static $instances = [];
	protected Request $req;
	protected Response $res;

	protected static function create_app()
	{
		// Get last app from instances
		$last_app = end(self::$instances);

		// Create a new app with last baseUrl
		$app = new Express($last_app->req->baseUrl);

		// Clone request and res to new app
		$app->req = clone $last_app->req;
		$app->res = clone $last_app->res;

		// Add access to app class in Request
		$app->req->app = $app;

		return $app;
	}
}
