<?php

namespace Core\Wireframe;

interface Object
{
	public static function config($config = []);
	public static function filter($method_name, $handle);
}
