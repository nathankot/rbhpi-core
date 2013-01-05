<?php

namespace Core\Test\Mock;

class Model extends \Core\Blueprint\Model
{
	public $name = 'Mock';

	public $schema = [
			'name' => 'string:required:name'
		,	'number' => 'string:number'
		,	'default' => 'string:number?:12345'
	];
}
