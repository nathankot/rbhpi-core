<?php

namespace Core\Wireframe\Merchant;

use Core\Prototype\Response as ResponsePrototype;

interface Response
{
	public static function renderSilent(ResponsePrototype $response);
	public static function render(ResponsePrototype $response);
}
