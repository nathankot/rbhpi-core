<?php

namespace Core\Wireframe\Merchant;

use Core\Prototype\Response as ResponsePrototype;

/**
 * The Response Merchant should provide a means of deploying replys to server requests.
 */
interface Response
{
	public static function serve(ResponsePrototype $response);
	public static function serveError($exception, $request);
	public static function getLastSentHeaders();
}
