<?php

namespace Core\Test\Merchant;

use Core\Test\Mock\Route as RouteMock;
use Core\Test\Mock\Controller as ControllerMock;
use Core\Prototype\Response as ResponsePrototype;
use Core\Merchant\Response as Subject;

class Response extends \Core\Test\Base
{
	public function init()
	{
		$this->message('Creating a Mock Response to be used for the views.');

		ResponsePrototype::config([
				'get_controller_handle' => function($name) {
					return new ControllerMock();
				}
		]);

		$route = new RouteMock();
		$this->response = new ResponsePrototype($route);
		$this->response->getResult()->setTemplate(ROOT.'/Core/Core/Test/Mock/MustacheTemplate.mustache');
		$this->response->getResult()->setLayout(null);
	}

	public function test()
	{
		$this->message('Testing headers are correct in response.');

		$this->message('Testing 200 OK header');
		ob_start(); Subject::serve($this->response); ob_end_clean();
		$headers = Subject::getLastSentHeaders();
		assert(in_array('Status: 200 OK', $headers));
		assert(in_array('Content-type: text/html', $headers));

		$this->message('Testing 404 Not Found header');
		$this->response->setStatus(404);
		ob_start(); Subject::serve($this->response); ob_end_clean();
		$headers = Subject::getLastSentHeaders();
		assert(in_array('Status: 404 Not Found', $headers));

		$this->message('Testing JSON content-type header');
		$this->response->setFormat('json');
		ob_start(); Subject::serve($this->response); ob_end_clean();
		$headers = Subject::getLastSentHeaders();
		assert(in_array('Status: 404 Not Found', $headers));
		assert(in_array('Content-type: application/json', $headers));
	}
}
