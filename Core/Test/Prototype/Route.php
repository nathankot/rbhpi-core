<?php

namespace Core\Test\Prototype;

use Core\Prototype\Route as Subject;
use Core\Prototype\Request;

class Route extends \Core\Test\Base
{

	public function testCreation()
	{
		////
		$this->message('Testing Route creation.');

		$request = new Request('One/Two/Three.json');
		$route = new Subject($request);
	}

	public function testParse()
	{
		Subject::connect('{controller}/{method}/{*args}', function($captured) {
			$result = [
					'controller' => $captured['controller']
				,	'method' => $captured['method']
				,	'args' => $captured['args']
			];
		});

		////
		$this->message('Testing Route parser');

		$request= new Request('One/Two/Three.json');
		$route = new Subject($request);

		assert($route->getController() === 'One');
		assert($route->getMethod() === 'Two');
		assert($route->getArgs() === ['Three']);
		assert($route->getFormat() === 'json');
	}

}
