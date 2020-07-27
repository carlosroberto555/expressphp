<?php

namespace ExpressPHP\Router;

class Response
{
	public $mounturl;

	public function __construct()
	{
		$this->mounturl = preg_replace('/\/\w+.php$/', '', $_SERVER['PHP_SELF']);
	}

	public function send($resp)
	{
		echo $resp;
	}

	public function sendFile($path)
	{
		// Get the mime-type
		$type = mime_content_type($path);
		$this->type($type);

		// Disable auto download
		$file = pathinfo($path, PATHINFO_BASENAME);
		$this->header('Content-Disposition', "inline; filename=\"$file\"");

		// Headers e filetime
		$filetime = filemtime($path);
		$headers = getallheaders();

		// Cache control
		$this->header('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $filetime));
		$this->header('Cache-Control', 'only-if-cached');

		// Se não tiver sido modificado, usa o cache
		if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $filetime)) {
			$this->status(304);
		} else {
			$this->header('Content-transfer-encoding', 'binary');
			$this->header('Content-length', filesize($path));
			$this->status(200);
			readfile($path);
		}

		$this->end();
	}

	public function json($resp)
	{
		$this->type('application/json');

		if (defined('DEBUG') && constant('DEBUG')) {
			echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode($resp);
		}
	}

	public function status($status = null)
	{
		return http_response_code($status);
	}

	public function header($key, $value)
	{
		header("$key: $value");
	}

	public function location($loc)
	{
		$this->header('Location', $this->mounturl . $loc);
	}

	public function type($type)
	{
		$this->header('Content-Type', "$type; charset=utf-8");
	}

	public function end($message = null)
	{
		exit($message);
	}

	public function __call($method, $args)
	{
		if (isset($this->$method)) {
			return call_user_func_array($this->$method, $args);
		}
	}
}
