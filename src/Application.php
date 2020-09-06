<?php

namespace ExpressPHP;

use ExpressPHP\Express;
use ExpressPHP\Router\{Request, Response};

trait Application
{
	protected static $instances = [];
	protected $req;
	protected $res;

	// Read-only props
	protected $_mountregexp, $_mountpath, $_mounturl;

	/**
	 * Creates a new express app props request and response
	 * @param string $mountpath A mount path app
	 */
	protected function create_app($mountpath)
	{
		// Guarda as instâncias do router
		self::$instances[] = $this;

		// Instancia o Request e o response
		$this->req = new Request;
		$this->res = new Response;

		if (empty($mountpath)) {
			$this->_mountpath = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
		} else {
			$this->_mountpath = $mountpath;
		}

		$this->_mountregexp = preg_replace('/(:\w+)/', '(\w+)', $this->_mountpath);
		$this->_mounturl = preg_replace('#(' . $this->_mountregexp . ').*#', '$1', $this->req->url);

		// Add access to app class in Request and set baseurl
		$this->req->app = $this;
		$this->req->baseUrl = $this->mounturl;
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
