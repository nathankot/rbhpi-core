<?php

namespace Core\Merchant;

use Core\Prototype\Response;

class View extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\View
{
	public static function init()
	{

	}

	public static function renderSilent(Response $response)
	{
		$format = $response->getFormat();
		$status = $response->getStatus();
		$headers = $response->getHeaders();
		$result = $response->getResult();
	}

	public static function render(Response $response)
	{
		echo self::renderSilent($response);
	}
}
