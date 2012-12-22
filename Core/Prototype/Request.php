<?php

namespace Core\Prototype;

/**
 * The Request object is responsible for taking a variety of paths and breaking them down.
 */
class Request extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\Request
{
	private static $config = [
			'available_formats' => array('html', 'json')
		,	'default_format' => 'html'
	];

	private $path;
	private $format;
	private $components;

	private function init()
	{
		$args = func_get_args();
		if (count($args) === 1) {
			$args = $args[0];
		}
		if (is_array($args)) {
			$args = implode('/', $args);
		}
		$this->path = '/' . trim($args, '/');
		$this->breakItDown();
	}

	private function breakItDown()
	{
		$components = explode('/', trim($this->path, '/'));
		$last = array_slice($components, -1, 1);
		if (strpos($last, '.') === 0) {
			if (in_array(substr($last, 1), self::$config['available_formats'])) {
				array_pop($components);
				$this->format = substr($last, 1);
			}
		}
		$this->components = $components;
		if (!$this->format) {
			$this->format = self::$config['default_format'];
		}
	}

	public function getFormat()
	{
		return $this->format;
	}

	public function getComponents()
	{
		return $this->components;
	}

	public function getPath()
	{
		return $this->path;
	}
}
