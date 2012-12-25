<?php

namespace Core\Test\Prototype;

use Core\Prototype\Route as Subject;
use Core\Prototype\Request;

class Route extends \Core\Test\Base
{

	public function testParse()
	{
		Subject::connect('{controller}/{method:@[A-e]*@}/{*args:integer}', function($captured) {
			return [
					'controller' => $captured['controller']
				,	'method' => $captured['method']
				,	'args' => $captured['args']
			];
		});

		Subject::connect('{one}/{two}/{three:email}', function($captured) {
			return [
					'controller' => $captured['two']
				,	'method' => $captured['one']
				,	'args' => [$captured['three']]
			];
		});

		Subject::connect('{controller}/{method}/{*args}', function($captured) {
			return [
					'controller' => $captured['controller']
				,	'method' => $captured['method']
				,	'args' => $captured['args']
			];
		});

		////
		$this->message('Testing Route parser: Basic Route');

		$request= new Request('One/Two/Three.json');
		$route = new Subject($request);

		assert($route->getController() === 'One');
		assert($route->getMethod() === 'Two');
		assert($route->getArgs() === ['Three']);
		assert($route->getFormat() === 'json');

		////
		$this->message('Testing Route parser: Regex filter, and Splat Integer Filter');

		$request = new Request('controller', 'abCde', '1234', '5678', '910');
		$route = new Subject($request);

		assert($route->getController() === 'controller');
		assert($route->getMethod() === 'abCde');
		assert($route->getArgs() === ['1234', '5678', '910']);
		assert($route->getFormat() === 'html');

		////
		$this->message('Testing Route parser: Complex route with email filter');

		$request = new Request('one/two/nk@nathankot.com');
		$route = new Subject($request);

		assert($route->getController() === 'two');
		assert($route->getMethod() === 'one');
		assert($route->getArgs() === ['nk@nathankot.com']);
		assert($route->getFormat() === 'html');
	}

	public function testDefaultHandler()
	{

	}

}
