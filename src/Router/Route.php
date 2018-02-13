<?php
namespace ExpressPHP\Router;

class Route {
	private $mounturl;

	public $path;
	public $stack;
	public $regexp;

	public $params;

	public $result_url;
	public $result_baseUrl;
	public $result_path;

	public function __construct($mounturl, $path, ...$callbacks)
	{
		$this->path = $path;
		// $this->stack = $callbacks;
		$this->mounturl = $mounturl;

		preg_match_all('/:(\w+)/', $path, $params);
		$this->params = $params[1];
	}

	public function matches($url, $exact = false)
	{
		$this->regexp = $this->regexp($this->path, $exact);
		preg_match("#$this->regexp#", $this->mounturl.$url, $matches);

		if (!empty($matches))
		{
			$this->result_url = $matches[0];
			$this->result_baseUrl = $matches[1];
			$this->result_path = $matches[2];
			
			if (empty(end($matches))) {
				array_pop($matches);
			} else {
				unset($matches[2]);
			}
			
			unset($matches[0], $matches[1]);
			$this->params = (object) array_combine($this->params, $matches);
			
			return true;
		}

		return false;
	}

	/**
	 * Gera o regex para a rota atual
	 */
	private function regexp($path, $exact = false)
	{
		$path = preg_replace('/(:\w+)/', '(\w+)', $path);
		$path = $path == '/' ? '' : $path;

		if ($exact) {
			return "^$this->mounturl($path)(/?)$";
		} else {
			return "$this->mounturl($path)(/?.*)";
		}
	}

	private function process_params() {

	}
}