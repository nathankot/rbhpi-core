<?php
/**
 * @version 0.1.0
 */

namespace Core\Prototype;

use Core\Prototype\Route;

/**
 * The response object encapsulates the response. It takes a `Core\Prototype\Route` and determines
 * the proper course of action. When `Self::execute()` is run, this course of action is executed
 * and stored in `$this->result`.
 *
 * This object should then be passed on to the appropriate view handler for handling.
 */
class Response extends \Core\Blueprint\Object implements
	\Core\Wireframe\Prototype\Response
{
	/**
	 * The injected Route.
	 * @var Core\Prototype\Route
	 */
	private $route;

	/**
	 * The request format ascertained from the Route.
	 * @var string
	 */
	private $format;

	/**
	 * The response HTTP Status.
	 * @var integer
	 */
	private $status = 200;

	/**
	 * Response headers that _should_ be passed.
	 * @var array
	 */
	private $headers = [];

	/**
	 * Result of the Controller action.
	 * @var mixed
	 */
	private $result;

	/**
	 * Take a `Route` as the dependency. **Runs** the proper controller action and stores the result.
	 * @param  Route  $route The route Object
	 * @return void
	 */
	public function init(Route $route)
	{
		# Default controller getter
		if (empty(self::$config['get_controller_handle'])) {
			self::config([
					'get_controller_handle' => function($name, $args = []) {
							$class_name = "\\App\\Controller\\{$name}";
							if (!class_exists($class_name)) {
								throw new \Exception\BadController("The controller `{$class_name}` does not exist!");
							}
							$reflect = new \ReflectionClass($class_name);
							return $reflect->newInstanceArgs($args);
					}
			]);
		}
		$this->route = $route;
		$this->format = $route->getFormat();
		$this->result = $this->execute();
	}

	/**
	 * Set the format of the Response.
	 * @param string $format i.e 'html', 'json'
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * Get the Format.
	 * @return string Format
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * Set the HTTP Status Code of the response.
	 * @param integer $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * Get the Status.
	 * @return string Status
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Add a header to the response. Newer headers will override older ones.
	 * @param string $header
	 */
	public function addHeader($header)
	{
		$this->headers[] = $header;
	}

	/**
	 * Get the set Headers.
	 * @return array Set headers.
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Replace all the headers.
	 * @param array $headers New Headers.
	 */
	public function setHeaders($headers)
	{
		$this->headers = $headers;
	}

	/**
	 * Get the result of the Controller action.
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * Run the Controller action and return the result.
	 * @return mixed Result from the controller.
	 */
	private function execute()
	{
		if (!is_callable(self::$config['get_controller_handle'])) {
			throw new \InvalidArgumentException("Core\\Prototype\\Response::\$config['get_controller_handle'] is empty!");
		}
		$controller = call_user_func_array(self::$config['get_controller_handle'], [$this->route->getController()]);
		if (!method_exists($controller, $this->route->getMethod())) {
			throw new \BadMethodCallException("The controller `{$this->route->getController()}` does not have method `{$this->route->getMethod()}`");
		}
		$result = call_user_func_array([$controller, $this->route->getMethod()], $this->route->getArgs());
		return $result;
	}
}
