<?php

namespace Core\Test\Prototype;

use Core\Prototype\Response as Subject;
use Core\Test\Mock\Route as RouteMock;
use Core\Test\Mock\Controller as ControllerMock;

class Response extends \Core\Test\Base
{
	public function test()
	{
		Subject::config([
				'get_controller_handle' => function($name) {
					return new ControllerMock();
				}
		]);

		$this->message('Testing Response Creation.');

		$route = new RouteMock();
		$response = new Subject($route);

		$this->message('Testing Response result');

	}

}
