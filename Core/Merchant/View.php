<?php

namespace Core\Merchant;

use Core\Prototype\Response;

class View extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\View
{
	protected static $config = [];

	public static function init()
	{
		self::config([
				'format_view_handlers' => [
						'html' => function(Response $response) {
							$view = $response->getResult();
							return $view->toHTML();
						}
					,	'json' => function(Response $response) {
							$view = $response->getResult();
							return $view->toJSON();
						}
				]
			,	'format_headers' => [
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
		return self::$config['formats'][$response->getFormat()]($response);
	}

	public static function render(Response $response)
	{
		self::config();

		$headers = $response->getHeaders();
		$format = $response->getFormat();
		$status = $response->getStatus();

		$headers = array_merge($headers, self::$config['format_headers'][$format]);
		$headers = array_merge($header, self::$config['status_headers'][$status]);

		foreach ($headers as $header) {
			header($header, true);
		}

		# Update the Response Object.
		$response->setHeaders($headers);

		echo self::renderSilent($response);
	}
}
