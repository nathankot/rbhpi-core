<?php

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
	private $route;

	private $format;

	private $status = 200;

	private $headers = [];

	private $result;

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

	public function setFormat($format)
	{
		$this->format = $format;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function addHeader($header)
	{
		$this->headers[] = $header;
	}

	public function getResult()
	{
		return $this->result;
	}

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
