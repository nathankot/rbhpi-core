<?php
/**
 * Converts data of the passed object into HTML.
 * @version 0.1.0
 */

return function($self, $args) {
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
};
