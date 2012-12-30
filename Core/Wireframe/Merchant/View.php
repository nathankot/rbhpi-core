<?php

namespace Core\Wireframe\Merchant;

use Core\Prototype\Response;

interface View
{
	public static function renderSilent(Response $response, $format = 'auto');
	public static function render(Response $response, $format = 'auto');
}
