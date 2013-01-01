<?php
/**
 * @version 0.1.0
 */

namespace Core\Prototype;

/**
 * The Request object is responsible for taking a variety of paths and breaking them down.
 */
class Request extends \Core\Blueprint\Object implements
	\Core\Wireframe\Prototype\Request
{
	/**
	 * Class-wide configuration.
	 * @var array
	 */
	protected static $config = [
			'available_formats' => []
		,	'default_format' => ''
		,	'default_method' => ''
	];

	/**
	 * URI Path of the route
	 * @var string
	 */
	private $path;

	/**
	 * Format of the route, defaults to `static::$config['default_format']`.
	 * @var string
	 */
	private $format;

	/**
	 * An array of route components, excluding the format.
	 * @var array
	 */
	private $components;

	/**
	 * Parse the given arguemnts. This class can accept:
	 *
	 * - An array
	 * - A list of arguments
	 * - A route URI string
	 *
	 * @return void
	 */
	protected function init()
	{
		$args = func_get_args();
		if (count($args) === 1) {
			$args = $args[0];
		}
		if (is_array($args)) {
			$args = implode('/', $args);
		}
		$this->path = '/' . trim($args, '/');
		$this->path = str_replace('/.', '.', $this->path);
		$this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : self::$config['default_method'];
		$this->breakItDown();
	}

	/**
	 * Break the path down into an array of components, and the format.
	 * @return void
	 */
	private function breakItDown()
	{
		$components = explode('/', trim($this->path, '/'));
		$last = array_pop($components);
		$last = explode('.', $last);
		$format = end($last);
		if (empty($format)) {
			$this->format = isset($_SERVER['HTTP_ACCEPT']) ? \Bitworking\Mimeparse::bestMatch(self::$config['available_formats'], $_SERVER['HTTP_ACCEPT']) : null;
		}
		if (in_array($format, self::$config['available_formats'])) {
			$this->format = array_pop($last);
		}
		$components = array_merge($components, array_filter($last));
		$this->components = $components;
		if (empty($this->format)) {
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

	public function getMethod()
	{
		return $this->method;
	}
}
