<?php
/**
 * @version 0.1.0
 */

namespace Core\Prototype;

use Core\Prototype\Route;
use Core\Blueprint\View;

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
	 * Response configuration.
	 * @var array
	 */
	protected static $config = [
			'format_headers' => []
		,	'status_headers' => []
		,	'exception_map' => [
					"\\BadRequest" => 404
				,	"\\LogicException" => 500
				,	"\\RuntimeException" => 400
				,	"\\Exception" => 500
			]
		,	'get_controller_handle' => null
	];

	/**
	 * Stores headers that were last sent by this class.
	 * @var array
	 */
	private static $last_sent_headers = [];

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
	 * Default configuration for this class.
	 * @return void
	 */
	public static function preConfig()
	{
		# Default controller getter
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

	/**
	 * Get the headers that were sent. Primarily used for testing in CLI environments.
	 * @return array Headers last sent.
	 */
	public static function getLastSentHeaders()
	{
		return self::$last_sent_headers;
	}

	/**
	 * Take a `Route` as the dependency. **Runs** the proper controller action and stores the result.
	 * @param  Route  $route The route Object
	 * @return void
	 */
	public function init(Route $route)
	{
		self::config();
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
	 * Get the Route that this Response is using.
	 * @return Core\Prototype\Route
	 */
	public function getRoute()
	{
		return $this->route;
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
	 * Serve the response with appropriate headers.
	 * @return void
	 */
	public function serve()
	{
		self::config();

		$headers = $this->getHeaders();
		$format = $this->getFormat();
		$status = $this->getStatus();

		$headers = array_merge($headers, self::$config['format_headers'][$format]);
		$headers = array_merge($headers, self::$config['status_headers'][$status]);

		foreach ($headers as $header) {
			header($header, true);
		}

		self::$last_sent_headers = $headers;

		# Update the Response Object.
		$this->setHeaders($headers);

		$view = $this->getResult();
		$method_name = "to".strtoupper($this->getFormat());

		if (!method_exists($view, $method_name)) {
			throw new \BadMethodException(get_class($view)." does not have the method `::{$method_name}`");
		}

		echo $view->$method_name();
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
		if (!($result instanceof View)) {
			throw new \UnexpectedValueException("The Response from a Controller should be an instance of `Core\Blueprint\View`");
		}
		return $result;
	}

}
