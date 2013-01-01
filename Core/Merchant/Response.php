<?php
/**
 * @version 0.1.0
 */

namespace Core\Merchant;

use Core\Prototype\Response as ResponsePrototype;

class Response extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\Response
{
	protected static $config = [
			'format_headers' => []
		,	'status_headers' => []
	];

	public static function renderSilent(ResponsePrototype $response)
	{
		self::config();
		$view = $response->getResult();
		$method_name = "to".strtoupper($response->getFormat());
		if (!method_exists($view, $method_name)) {
			throw new \BadMethodException(get_class($view)." does not have the method `::{$method_name}`");
		}
		return $view->$method_name();
	}

	public static function render(ResponsePrototype $response)
	{
		self::config();

		$headers = $response->getHeaders();
		$format = $response->getFormat();
		$status = $response->getStatus();

		$headers = array_unshift($headers, self::$config['format_headers'][$format]);
		$headers = array_unshift($headers, self::$config['status_headers'][$status]);

		foreach ($headers as $header) {
			header($header, true);
		}

		# Update the Response Object.
		$response->setHeaders($headers);

		echo self::renderSilent($response);
	}
}
