<?php

namespace Core\Test\Prototype;

use Core\Prototype\Response as Subject;
use Core\Test\Mock\Route as RouteMock;

class Response extends \Core\Test\Base
{
	public function testCreation()
	{
		$route = new RouteMock();
		$response = new Subject($route);
	}

}
