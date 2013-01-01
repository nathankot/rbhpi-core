<?php

namespace Core\Test\Mock;

use Core\Test\Mock\View;

class Controller extends \Core\Blueprint\Controller
{
	public function method()
	{
		$data = [
				'numbers' => ['one', 'two', 'three']
			,	'boolean' => true
		];
		return new View($data);
	}
}
