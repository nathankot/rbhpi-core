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

		$result = $response->getResult();
		assert($result instanceof \Core\Blueprint\View);

		$this->message('Testing Response format');

		$result = $response->getFormat();
		assert($result === 'html');

		$this->message('Testing Response Status');

		$result = $response->getStatus();
		assert($result === 200);

		$this->message('Testing Response serving.');
		$response->getResult()->setTemplate(ROOT.'/Core/Core/Test/Mock/MustacheTemplate.mustache');
		$response->getResult()->setLayout(null);

		$this->message('Testing headers are correct in the response');

		$this->message('Testing 200 OK header');
		ob_start(); $response->serve(); ob_end_clean();
		$headers = $response->getLastSentHeaders();
		assert(in_array('Status: 200 OK', $headers));
		assert(in_array('Content-type: text/html', $headers));

		$this->message('Testing 404 Not Found header');
		$response->setStatus(404);
		ob_start(); $response->serve(); ob_end_clean();
		$headers = $response->getLastSentHeaders();
		assert(in_array('Status: 404 Not Found', $headers));

		$this->message('Testing JSON content-type header');
		$response->setFormat('json');
		ob_start(); $response->serve(); ob_end_clean();
		$headers = $response->getLastSentHeaders();
		assert(in_array('Status: 404 Not Found', $headers));
		assert(in_array('Content-type: application/json', $headers));
	}
}
