<?php
/**
 * Bootstrap sequence loaded and ready? Good. Let's begin.
 * @author Nathan Kot <nk@nathankot.com>
 * @version 0.1.0
 */

if (php_sapi_name() !== 'cli') {
	try {
		$request = new \Core\Prototype\Request();
		$route = new \Core\Prototype\Route($request);
		$response = new \Core\Prototype\Response($route);
		$response->serve();
	} catch (Exception $e) {
		# @todo : Do something with the caught exception.
	}
}

# Done.
