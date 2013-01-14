<?php

namespace Core\Test\Prototype;

use Core\Prototype\Route as Subject;
use Core\Prototype\Request as RequestPrototype;

class Route extends \Core\Test\Base
{

	public function testParse()
	{
		Subject::connect('/', function() {
			return [
					'controller' => 'root'
				,	'method' => 'success'
				,	'args' => ['three']
			];
		});

		Subject::connect('this/is/a/test', function($captured) {
			return [
					'controller' => 'test'
				,	'method' => 'success'
				,	'args' => ['three']
			];
		});

		Subject::connect('opt/{optional}?/three', function($captured) {
			return [
					'controller' => 'one'
				,	'method' => $captured['optional']
				,	'args' => ['three']
			];
		});

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
		$this->message('Testing Super Basic Route');

		$request= new RequestPrototype(['path' => 'this/is/a/test.json']);
		$route = new Subject($request);

		assert($route->getController() === 'test');
		assert($route->getMethod() === 'success');


		////
		$this->message('Testing Route parser: Basic Route');

		$request= new RequestPrototype(['path' => 'One/Two/Three.json']);
		$route = new Subject($request);

		assert($route->getController() === 'One');
		assert($route->getMethod() === 'Two');
		assert($route->getArgs() === ['Three']);
		assert($route->getFormat() === 'json');

		////
		$this->message('Testing Root Route detection');
		$request= new RequestPrototype(['path' => '']);
		$route = new Subject($request);
		assert($route->getController() === 'root');
		$request= new RequestPrototype(['path' => '/']);
		$route = new Subject($request);
		assert($route->getController() === 'root');

		////
		$this->message('Testing Route parser: Regex filter, and Splat Integer Filter');

		$request = new RequestPrototype(['path' => 'controller/abCde/1234/5678/910']);
		$route = new Subject($request);

		assert($route->getController() === 'controller');
		assert($route->getMethod() === 'abCde');
		assert($route->getArgs() === ['1234', '5678', '910']);
		assert($route->getFormat() === 'html');

		////
		$this->message('Testing Route parser: Complex route with email filter');

		$request = new RequestPrototype(['path' => 'one/two/nk@nathankot.com']);
		$route = new Subject($request);

		assert($route->getController() === 'two');
		assert($route->getMethod() === 'one');
		assert($route->getArgs() === ['nk@nathankot.com']);
		assert($route->getFormat() === 'html');

		////
		$this->message('Testing nil splat');

		$request = new RequestPrototype(['path' => 'controller/abCde']);
		$route = new Subject($request);

		assert($route->getController() === 'controller');
		assert($route->getMethod() === 'abCde');
		assert($route->getArgs() === []);

		////
		$this->message('Testing optional Route component');

		$request = new RequestPrototype(['path' => 'opt/two/three']);
		$route = new Subject($request);

		assert($route->getController() === 'one');
		assert($route->getMethod() === 'two');

		$request = new RequestPrototype(['path' => 'opt/three']);
		$route = new Subject($request);

		$result = $route->getMethod();
		assert(empty($result));

	}

}
