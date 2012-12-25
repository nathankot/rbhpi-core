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
		,	'filter' => '\Core\Merchant\Filter'
		,	'default_handle' => false
		,	'default_method' => 'index'
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
		self::$config['default_handle'] = self::$config['default_handle'] ?: function($captured, $request) {
			# UNTESTED YET, SUBJECT TO CHANGE
			$controller_class = "\\App\\Controller\\{$captured['controller']}";
			$prefixes = [strtolower($request->getMethod()).'_', ''];
			$prefixes_backup = $prefixes;
			$checked_index = false;
			do {
				$method = array_shift($prefixes) . $captured['method'];
				if (empty($prefixes) && !$checked_index) {
					array_unshift($captured['args'], $captured['method']);
					$captured['method'] = self::$config['default_method'];
					$prefixes = $prefixes_backup;
					$checked_index = true;
				}
			} while (!method_exists($controller_class, $method) && !empty($prefixes));
			return [
					'controller' => isset($captured['controller']) ? $captured['controller'] : null
				,	'method' => isset($captured['method']) ? $captured['method'] : null
				,	'args' => isset($captured['args']) ? $captured['args'] : []
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

	const MATCH_NAMES = '@{([\*\w]*):?.*?}@';

	/**
	 * Find the best controller, method, and args using the given request path, and pre-configured routes.
	 * @return void
	 */
	private function breakItDown()
	{
		$components = $this->request->getComponents();
		$path = $this->request->getPath();
		foreach (self::$config['routes'] as $route) {
			$captured = [];
			$names = [];
			if (preg_match($route['match'], $path, $captured) !== 1) {
				continue;
			}
			array_shift($captured);
			preg_match_all(self::MATCH_NAMES, $route['route'], $names, PREG_SET_ORDER);
			$names = array_map(function($value) {
				return $value[1];
			}, $names);
			$captured = array_combine($names, $captured);
			foreach ($captured as $name => $component) {
				if (strpos($name, '*') === 0) {
					$new_name = substr($name, 1);
					$captured[$new_name] = explode('/', $component);
					unset($captured[$name]);
				}
			}
			break;
		}
		if (empty($captured)) {
			throw new \Exception\BadRequest("Could not find a matching route for `{$path}`");
		}
		$result = $route['handle']($captured, $this->request);
		$this->controller = $result['controller'];
		$this->method = $result['method'];
		$this->args = $result['args'];
	}

	const MATCH_WRAPPER = '@^/?%s(?:\.[a-z]*)?$@';
	const MATCH_COMPONENT = '(%s)/?';
	const SPLAT_MATCH_COMPONENT = '((?:%s/?)*?)';
	const DEFAULT_MATCH = '[\d\w\_\-\%]*';

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
			$component_parts = explode(':', trim($component, '{}'));
			if (count($component_parts) === 2) {
				$filter_class = self::$config['filter'];
				$filters = explode('|', $component_parts[1]);
				if (
					count($filters) === 1 &&
					trim($filters[0], '@') !== $filters[0]
				) {
					$component = trim($filters[0], '@');
				} else {
					foreach ($filters as $filter) {
						if (class_exists($filter_class) && $filter_class::checkExist($filter)) {
							$component = $filter_class::getRegexp($filter);
						} else {
							$component = self::DEFAULT_MATCH;
						}
					}
				}
			} else {
				$component = self::DEFAULT_MATCH;
			}
			if (strpos($component_parts[0], '*') === 0) {
				$component = sprintf(self::SPLAT_MATCH_COMPONENT, $component);
			} else {
				$component = sprintf(self::MATCH_COMPONENT, $component);
			}
			$match_components .= $component;
		}
		$regex = sprintf(self::MATCH_WRAPPER, $match_components);
		return $regex;
	}

	/**
	 * Getter for the best controller.
	 * @return string Controller.
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Getter for the best method.
	 * @return string Method.
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Getter for the best arguments.
	 * @return array Arguments.
	 */
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * Getter for the best format.
	 * @return string Format.
	 */
	public function getFormat()
	{
		return $this->format;
	}
}
