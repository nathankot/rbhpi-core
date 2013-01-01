<?php
/**
 * Sets up default response formats for the framework.
 * @version 0.1.0
 */

\Core\Prototype\Request::config([
		'available_formats' => array('html', 'json')
	,	'default_format' => 'html'
	,	'default_method' => 'GET'
]);

\Core\Merchant\Response::config([
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

\Core\Blueprint\View::config([
		'mustache_escape' => function($text) {
			return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
		}
	,	'mustache_helpers' => []
]);

\Core\Blueprint\View::adapt('toJSON', function($self, $args) {
	return json_encode($self->getData());
});

\Core\Blueprint\View::adapt('toHTML', function($self, $args) {
	$layout = $self->getLayout();

	$mustache = new \Mustache_Engine([
			'cache' => ROOT.'/_tmp'
		,	'loader' => new \Core\Prototype\MustacheLoader(ROOT.'/App/Template/')
		,	'partials_loader' => new \Core\Prototype\MustacheLoader(ROOT.'/App/Template/Partial/')
		,	'escape' => \Core\Blueprint\View::getConfig()['mustache_escape']
		,	'helpers' => \Core\Blueprint\View::getConfig()['mustache_helpers']
	]);
	$template = $mustache->loadTemplate($self->getTemplate());
	$render = $template->render($self);

	if ($layout !== null) {
		$self->content = $render;
		$layout_mustache = new \Mustache_Engine([
				'cache' => ROOT.'/_tmp'
			,	'loader' => new \Core\Prototype\MustacheLoader(ROOT.'/App/Template/Layout')
			,	'partials_loader' => new \Core\Prototype\MustacheLoader(ROOT.'/App/Template/Partial/')
			,	'escape' => \Core\Blueprint\View::getConfig()['mustache_escape']
			,	'helpers' => \Core\Blueprint\View::getConfig()['mustache_helpers']
		]);
		$layout_template = $layout_mustache->loadTemplate($self->getLayout());
		$render = $layout_template->render($self);
	}

	return $render;
});
