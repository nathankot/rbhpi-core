<?php

namespace Core\Test\Blueprint;

use Core\Test\Mock\View as ViewMock;

class View extends \Core\Test\Base
{
	public function test()
	{
		$this->message("Creating Mock data & view for testing.");

		$data = [
				'numbers' => ['one', 'two', 'three']
			,	'boolean' => true
		];

		$view = new ViewMock($data);
		$view->setTemplate(ROOT.'/Core/Core/Test/Mock/MustacheTemplate.mustache');
		$view->setLayout(null);

		$this->message('Testing HTML rendering with a Mustache Template, and without a layout.');

		$result = $view->toHTML();

		$this->message('Making sure that iteration is working.');
		assert(strpos($result, "one\n\t\ttwo\n\t\tthree") !== false);
		$this->message('Making sure that method calling is working.');
		assert(strpos($result, 'one-two-three') !== false);
		$this->message('Making sure that partials are loading.');
		assert(strpos($result, 'partial') !== false);

		$this->message('Testing HTML rendering with a Mustache Template & Layout');

		$view->setLayout(ROOT.'/Core/Core/Test/Mock/MustacheLayout.mustache');
		$result = $view->toHTML();
		assert(strpos($result, 'layout') !== false);

		$this->message("Testing JSON rendering.");

		$result = $view->toJSON();
		$result = json_decode($result, true);
		assert($result === $view->getData());

	}
}
