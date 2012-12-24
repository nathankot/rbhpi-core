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
	\Core\Wireframe\Prototype\Route
{
	/**
	 * Class-wide configuration
	 * @var array
	 */
	protected static $config = [
			'routes' => []
		,	'filter' => 'Core\Merchant\Filter'
		,	'default_handle' => false
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
		# Set the default handle if it doesn't exist
		self::$config['default_handle'] = self::$config['default_handle'] ?: function($captured) {
			return [
					'controller' => $captured['controller']
				,	'method' => $captured['method']
				,	'args' => $captured['args']
			];
		};
		## End, start your average method logic: ##
		if (!is_string($route)) {
			trigger_error('Core\Prototype\Route::connect($route, $handle) must take a string as $route.', E_USER_ERROR);
		}
		if ($handle && !is_callable($handle)) {
			trigger_error('Core\Prototype\Route::connect($route, $handle) must take a callable as $handle.', E_USER_ERROR);
		}
		if (!$handle) {
			$handle = self::$config['default_handle'];
		}
		$match = self::generateMatch($route);
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

	const MATCH_NAMES = '@{(\w*):?\w*?}@';

	private function breakItDown()
	{
		$components = $this->request->getComponents();
		$path = $this->request->getPath();
		foreach (self::$config['routes'] as $route) {
			if (preg_match($route['match'], $path, $captured) !== 1) {
				continue;
			}
			array_shift($captured);
			preg_match_all(self::MATCH_NAMES, $route['route'], $matches, $names);
			if (count($captured) > count($names)) {
				$last = array_slice($captured, count($names) - 1, count($captured) - count($names));
				$captured = array_slice($captured, 0, count($names) - 1);
				$captured[] = $last;
			}
			$captured = array_combine($names, $captured);
			break;
		}
		if (empty($captured)) {
			throw new \Exception\BadRequest("Could not find a matching route for `{$path}`");
		}
		$result = $route['handle']($captured);
		$this->controller = $result['controller'];
		$this->method = $result['method'];
		$this->args = $result['args'];
	}

	const MATCH_WRAPPER = '@^%s(?:\.[a-z]*)$@';
	const MATCH_COMPONENT = '(%s)/?';
	const SPLAT_MATCH_COMPONENT = '(%s/?)*';
	const DEFAULT_MATCH = '[\w\.\_\-]*';

	/**
	 * Create a regex match for a given route.
	 * @param  string $route Route
	 * @return string        Regex which matches the route
	 */
	public static function generateMatch($route)
	{
		$match_components = '';
		$components = explode('/', trim($route, '/'));
		foreach ($components as $component) {
			$component_parts = explode(':', $component);
			if (count($component_parts) === 2) {
				$filter_class = self::$config['filter'];
				if (class_exists($filter_class, false) && method_exists($filter_class, $component_parts[1])) {
					$component = $filter_class::$component_parts[1]();
				} else {
					$component = self::DEFAULT_MATCH;
				}
			} else {
				$component = self::DEFAULT_MATCH;
			}
			if (strpos($component_parts[0], '*') === 0) {
				$component = sprintf(self::SPLAT_MATCH, $component);
			} else {
				$component = sprintf(self::MATCH_COMPONENT, $component);
			}
			$match_components .= $component;
		}
		$regex = sprintf(self::MATCH_WRAPPER, $match_components);
		return $regex;
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
