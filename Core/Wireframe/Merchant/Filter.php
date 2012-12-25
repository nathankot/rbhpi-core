<?php

namespace Core\Wireframe\Merchant;

interface Filter
{
	public static function getRegexp($filter_name);
	public static function checkExist($filter_name);
}
