<?php

namespace Core\Test\Merchant;

use Core\Test\Mock\Route as RouteMock;
use Core\Test\Mock\Controller as ControllerMock;
use Core\Prototype\Response;
use Core\Merchant\Response as Subject;

class Response extends \Core\Test\Base
{
	public function init()
	{
		$this->message('Creating a Mock Response to be used for the views.');

		Response::config([
				'get_controller_handle' => function($name) {
					return new ControllerMock();
				}
		]);

		$route = new RouteMock();
		$this->response = new Response($route);
	}

	public function test()
	{
		$this->message('Testing render of HTML view');

		$response_1 = clone $this->response;
		$result = Subject::renderSilent($response_1);

		$this->message('Testing render of JSON view');

		$response_2 = clone $this->response;
		$response_2->setFormat('json');
		$result = Subject::renderSilent($response_2);

		$this->message('Testing render of erronous Status');

		$response_3 = clone $this->response;
		$response_3->setStatus(400);
		$result = Subject::renderSilent($response_3);

		$response_3->setStatus(403);
		$result = Subject::renderSilent($response_3);

		$response_3->setStatus(500);
		$result = Subject::renderSilent($response_3);
	}
}
