<?php

namespace ExpressPHP;

use ExpressPHP\Router\{Request, Response};

trait Application
{
	protected static $instances = [];
	protected Request $req;
	protected Response $res;

	/**
	 * Creates a new express app props request and response
	 * @param string $mounturl URL that app listens
	 */
	protected function create_app($mounturl)
	{
		// Instancia o Request e o response
		$this->req = new Router\Request;
		$this->res = new Router\Response;

		// Add access to app class in Request and set baseurl
		$this->req->app = $this;
		$this->req->baseUrl = $mounturl;
	}

	protected static function create_router()
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
