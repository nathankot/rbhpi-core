<?php

namespace Core\Test;

use \ReflectionClass;
use \ReflectionMethod;
use Core\Merchant\Color;

class Base {
	/**
	 * Automatically invoke every public method of the descendent test class
	 */
	public function __construct()
	{
		if (is_callable(array($this, 'init'))) {
			$this->init();
		}
		$reflection = new ReflectionClass($this);
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			if (strpos($method->name, '__') === 0 || strpos($method->name, 'init') === 0) {
				continue;
			}
			$method->invoke($this);
		}
	}

	protected function message($e)
	{
		message(Color::dim($e));
	}

	protected function assert($statement)
	{
		return assert($statement);
	}
}
