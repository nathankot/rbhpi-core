<?php
/**
 * Sets up default response formats for the framework.
 * @version 0.1.0
 */

# Accepted response formats, and defaults
\Core\Prototype\Request::config([
		'available_formats' => [
				'html' => ['text/html']
			,	'json' => ['application/json', 'application/x-javascript', 'text/javascript']
		]
	,	'default_format' => 'html'
	,	'default_method' => 'GET'
]);

# Headers that the Response will send depending on the response format
\Core\Prototype\Response::config([
		'format_headers' => [
				'html' => ['Content-type: text/html']
			,	'json' => ['Content-type: application/json']
		]
	,	'status_headers' => [
				'200' => ['Status: 200 OK']
			,	'404' => ['Status: 404 Not Found']
			,	'403' => ['Status: 403 Forbidden']
			,	'503' => ['Status: 504 Service Unavailable']
			, '500' => ['Status: 500 Internal Server Error']
		]
]);

# Escape function and helpers when using the Mustache Templating Engine
\Core\Blueprint\View::config([
		'mustache_escape' => function($text) {
			return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
		}
	,	'mustache_helpers' => []
]);

\Core\Blueprint\View::adaptSet('Core\Adapter\Mustache');
