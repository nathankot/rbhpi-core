<?php
/**
 * @version 0.1.0
 */

namespace Core\Test\Prototype;

use Core\Prototype\Request as Subject;

/**
 * Test the Request object.
 */
class Request extends \Core\Test\Base
{
	/**
	 * Test its ability to decipher different variations of URI's
	 */
	public function decipherURI()
	{

		Subject::config(['available_formats' => ['format']]);

		////
		$this->message('Testing Request with basic URI string');

		$mock_uri = '/one/two/three.format';
		$request = new Subject($mock_uri);

		assert($request->getFormat() === 'format');
		assert($request->getComponents() === ['one', 'two', 'three']);
		assert($request->getPath() === $mock_uri);

		////
		$this->message('Testing Request with multi-parameter route');

		$request = new Subject('one', 'two', 'three', '.format');

		assert($request->getFormat() === 'format');
		assert($request->getComponents() === ['one', 'two', 'three']);
		assert($request->getPath() === $mock_uri);

		////
		$this->message('Testing Request with an array-route');

		$request = new Subject(['one', 'two', 'three', '.format']);

		assert($request->getFormat() === 'format');
		assert($request->getComponents() === ['one', 'two', 'three']);
		assert($request->getPath() === $mock_uri);
	}
}
