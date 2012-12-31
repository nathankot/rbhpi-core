<?php
/**
 * @version 0.1.0
 */

namespace Core\Merchant;

use Core\Prototype\Response;

class Response extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\Response
{
	protected static $config = [];

	public static function init()
	{
		self::config([
				'format_headers' => [
						'html' => ['Content-type: text/html']
					,	'json' => ['Content-type: application/json']
				]
			,	'status_headers' => [
						'200' => ['Status: 200 OK']
					,	'404' => ['Status: 404 Not Found']
					,	'403' => ['Status: 493 Forbidden']
					, '500' => ['Status: 500 Internal Server Error']
				]
		]);
	}

	public static function renderSilent(Response $response)
	{
		self::config();
		$view = $response->getResult();
		$method_name = "to".strtoupper($response->getFormat());
		if (!method_exists($view, $method_name)) {
			throw new \BadMethodException(get_class($view)." does not have the method `::{$method_name}`");
		}
		return $view->$method_name();
	}

	public static function render(Response $response)
	{
		self::config();

		$headers = $response->getHeaders();
		$format = $response->getFormat();
		$status = $response->getStatus();

		$headers = array_merge($headers, (array)self::$config['format_headers'][$format]);
		$headers = array_merge($header, (array)self::$config['status_headers'][$status]);

		foreach ($headers as $header) {
			header($header, true);
		}

		# Update the Response Object.
		$response->setHeaders($headers);

		echo self::renderSilent($response);
	}
}
