<?php
/**
 * @version 0.1.0
 */

namespace Core\Merchant;

use Core\Prototype\Response as ResponsePrototype;

class Response extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\Response
{
	/**
	 * Stores headers that were last sent by this merchant.
	 * @var array
	 */
	private static $last_sent_headers = [];

	/**
	 * Class configuration.
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
	];

	/**
	 * Serve a `Core\Prototype\Response` Object with the appropriate headers.
	 * @param  ResponsePrototype $response Response Object
	 * @return void
	 */
	public static function serve(ResponsePrototype $response)
	{
		self::config();

		$headers = $response->getHeaders();
		$format = $response->getFormat();
		$status = $response->getStatus();

		$headers = array_merge($headers, self::$config['format_headers'][$format]);
		$headers = array_merge($headers, self::$config['status_headers'][$status]);

		foreach ($headers as $header) {
			header($header, true);
		}

		self::$last_sent_headers = $headers;

		# Update the Response Object.
		$response->setHeaders($headers);

		$view = $response->getResult();
		$method_name = "to".strtoupper($response->getFormat());

		if (!method_exists($view, $method_name)) {
			throw new \BadMethodException(get_class($view)." does not have the method `::{$method_name}`");
		}

		echo $view->$method_name();
	}

	public static function serveError($exception, $request)
	{
		foreach (self::$config['exception_map'] as $class => $status) {
			if ($exception instanceof $class) {

			}
		}
	}

	/**
	 * Get the headers that were sent. Primarily used for testing in CLI environments.
	 * @return array Headers last sent.
	 */
	public static function getLastSentHeaders()
	{
		return self::$last_sent_headers;
	}
}
