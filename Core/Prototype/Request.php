<?php
/**
 * @version 1.0.0
 */

namespace Core\Prototype;

/**
 * The Request object is responsible for taking a variety of paths and breaking them down.
 * It then provides the ability to divert the request to another RBHP server.
 */
class Request extends \Core\Blueprint\Object implements
	\Core\Wireframe\Prototype\Request
{

	use \Core\Augmentation\HTTP;

	/**
	 * Class-wide configuration.
	 * @var array
	 */
	protected static $config = [
			'available_formats' => []
		,	'default_format' => ''
		,	'default_method' => ''
		,	'rbhp_injection_token' => '__RBHP_MAKE_REQUEST'
	];

	/**
	 * URI Path of the route
	 * @var string
	 */
	private $path;

	/**
	 * The host in which to execute the route from, defaults to self.
	 * @var string
	 */
	private $host = 'localhost';

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
	 * The payload of the request (Either from the request body or POST)
	 * @var mixed
	 */
	private $payload;

	/**
	 * Parse the given arguemnts. This class can accept:
	 *
	 * - An array
	 * - A list of arguments
	 * - A route URI string
	 *
	 * @return void
	 */
	public function init($request_components = [
			'path' => null
		,	'payload' => null
		,	'host' => null
		,	'method' => null
		,	'format' => null
	])
	{
		$guessed_request_components = array_filter($this->getRequest());
		$request_components = array_filter($request_components);

		$request_components = array_merge($guessed_request_components, $request_components);

		$this->path = $request_components['path'] ?: null;
		$this->payload = $request_components['payload'] ?: null;
		$this->host = $request_components['host'] ?: null;
		$this->method = $request_components['method'] ?: null;
		$this->format = $request_components['format'] ?: null;

		$this->breakToComponents();
	}

	/**
	 * Inject this request to another server via an HTTP request, and get the response.
	 * @return mixed The response from the server.
	 */
	public function injectTo($host = null)
	{
		$this->host = $host ?: $this->getHost();
		return $this->injectRoute($this);
	}

	/**
	 * For idiomatic proxy for injectTo without an argument.
	 * @return mixed Response from the server.
	 */
	public function inject()
	{
		return $this->injectTo(null);
	}

	/**
	 * Break the path down into an array of components, and the format.
	 * @return void
	 */
	private function breakToComponents()
	{
		$components = explode('/', trim($this->path, '/'));

		$last = array_pop($components);
		$last = explode('.', $last);
		$format = end($last);

		if (!empty($format) && in_array($format, self::$config['available_formats'])) {
			$this->format = $format;
			array_pop($last);
		}

		$components = array_merge($components, array_filter($last));

		$this->components = $components;

		if (empty($this->format)) {
			$this->format = self::$config['default_format'];
		}

		$this->path = '/'.trim($this->path, '/');
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

	public function getHost()
	{
		return $this->host;
	}

	public function getPayload()
	{
		return $this->payload;
	}
}
