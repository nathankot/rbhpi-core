<?php

namespace Core\Test\Mock;

/**
 * Mock Route Object
 */
class Route extends \Core\Prototype\Route
{
	public function __construct() {}

	public function getController()
	{
		return 'Controller';
	}

	public function getMethod()
	{
		return 'Method';
	}

	public function getArgs()
	{
		return ['One', 'Two', 'Three'];
	}

	public function getFormat()
	{
		return self::$config['test_format'] ?: 'html';
	}
}
