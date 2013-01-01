<?php
/**
 * Bootstrap sequence loaded and ready? Good. Let's begin.
 * @author Nathan Kot <nk@nathankot.com>
 * @version 0.1.0
 */

if (php_sapi_name() !== 'cli') {
	try {
		$request = new \Core\Prototype\Request($_SERVER['REQUEST_URI']);
		$route = new \Core\Prototype\Route($request);
		$response = new \Core\Prototype\Response($route);
		\Core\Merchant\Response::serve($response);
	} catch (Exception $e) {
		\Core\Merchant\Response::serveError($e, $request);
	}
}

# Done.
