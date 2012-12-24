<?php
/**
 * @version 0.1.0
 */

namespace Core\Prototype;

use Core\Prototype\Request;

/**
 * The route object takes a `Core\Prototype\Request` and breaks it down into
 * interpretable components that can be used to create a response.
 */
class Route extends \Core\Blueprint\Object implements
	\Core\Wireframe\Route
{
	/**
	 * Class-wide configuration
	 * @var array
	 */
	protected static $config = [
			'routes' => []
		,	'filter' => 'Core\Merchant\Filter'
	];

	/**
	 * The encapsulated Request Object.
	 * @var Core\Prototype\Request
	 */
	private $request;

	/**
	 * Format given by the Request Object
	 * @var string
	 */
	private $format;

	/**
	 * Controller returned after parsing.
	 * @var string
	 */
	private $controller;

	/**
	 * Method returned after parsing.
	 * @var string
	 */
	private $method;

	/**
	 * An array of arguments returned after parsing.
	 * @var array
	 */
	private $args;

	/**
	 * Add a new route to the class-wide configuration.
	 * @param  string  $route  Route URI
	 * @param  callable $handle Handles the captured route variables.
	 * @return void
	 */
	public static function connect($route, $handle = false)
	{
		if (!is_string($route)) {
			trigger_error('Core\Prototype\Route::connect($route, $handle) must take a string as $route.', E_USER_ERROR);
		}
		if ($handle && !is_callable($handle)) {
			trigger_error('Core\Prototype\Route::connect($route, $handle) must take a callable as $handle.', E_USER_ERROR);
		}
		$match = $this->generateMatch($route);
		self::$config['routes'][] = [
				'route' => $route
			,	'handle' => $handle
			,	'match' => $match
		];
	}

	/**
	 * Take the request and save it, also get the format from the Request.
	 * @param  Request $request The encapsulated Request object
	 * @return void
	 */
	public function init(Request $request)
	{
		$this->request = $request;
		$this->format = $request->getFormat();
		$this->breakItDown();
	}

	private function breakItDown()
	{
		$components = $this->request->getComponents();
		$path = $this->request->getPath();
	}

	const MATCH_WRAPPER = '@^%s(?:\.[a-z]*)$@';
	const MATCH_COMPONENT = '(%s)/?';

	/**
	 * Create a regex match for a given route.
	 * @param  string $route Route
	 * @return string        Regex which matches the route
	 */
	private function generateMatch($route)
	{

	}

	public function getController()
	{
		return $this->controller;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getArgs()
	{
		return $this->args;
	}

	public function getFormat()
	{
		return $this->format;
	}
}
