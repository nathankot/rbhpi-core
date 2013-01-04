<?php

namespace Core\Test\Augmentation;

use Core\Test\Mock\Adaptable as AdaptableMock;

class Adaptable extends \Core\Test\Base
{
	public function test()
	{
		$this->message('Creating Mock Adaptable Object.');

		$adaptable = new AdaptableMock();

		$this->message('Creating Adapter for Adaptable method `method()`');

		AdaptableMock::adapt('method', function($self, $args) {
			$this->message('Making sure that the first argument of the Adapter method is an instance of the Object');
			assert($self instanceof AdaptableMock);
			return 'test';
		});

		$this->message('Testing use of the Adapter');

		$result = $adaptable->method();
		assert($result === 'test');

		$this->message('Testing creation and use of an arbitrary Adapter.');

		AdaptableMock::adapt('arb', function($self, $args){
			return 'test';
		});

		$result = $adaptable->arb();
		assert($result === 'test');

		$this->message('Testing the use of a folder-set of adapters.');
		AdaptableMock::adaptSet('Core\Test\Mock\Adapter');
		assert($adaptable->one() === 'one');
		assert($adaptable->two() === 'two');
	}
}
