<?php

namespace ExpressPHP\Router;

class Route
{
	private $mounturl;

	public $path;
	public $regexp;
	public $param_regexp = '[\w\d._]+';

	public $params;

	public $result_url;
	public $result_baseUrl;
	public $result_path;

	public function __construct($mounturl, $path)
	{
		$this->path = $path;
		$this->mounturl = $mounturl;
		$this->params = new \stdClass;

		preg_match_all('/\/:(\w+)/', $path, $params);

		foreach ($params[1] as $value) {
			$this->params->$value = null;
		}
	}

	public function matches($url, $exact = false)
	{
		$this->regexp = $this->regexp($this->path, $exact);
		preg_match("#$this->regexp#", $this->mounturl . $url, $matches);

		if (!empty($matches)) {
			$this->result_url = $matches[0];
			$this->result_baseUrl = $matches[1];
			$this->result_path = $matches[2];
			$this->match_params($url);

			return true;
		}

		return false;
	}

	/**
	 * Match params route path to url
	 * @param string $url The url to extract params
	 */
	private function match_params(string $url)
	{
		foreach ($this->params as $key => $value) {
			// Create path regex to get param
			$regexp = $this->param_regexp;
			$path_regexp = preg_replace(["/\/:$key/", "/\/:\w+/"], ["/($regexp)", "/$regexp"], $this->path);

			// Regex the url
			preg_match("#$path_regexp#", $url, $matches);

			// Get the group 1 result
			$this->params->$key = $matches[1];
		}
	}

	/**
	 * Gera o regex para a rota atual
	 */
	private function regexp($path, $exact = false)
	{
		$path = preg_replace('/(:\w+)/', $this->param_regexp, $path);
		$path = $path == '/' ? '' : $path;

		if ($exact) {
			return "^$this->mounturl($path)(/?)$";
		} else {
			return "$this->mounturl($path)(/?.*)";
		}
	}
}
