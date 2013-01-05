<?php

namespace Core\Test\Mock;

class Adaptable extends \Core\Blueprint\Object
{
	use \Core\Augmentation\Adaptable;

	public function method()
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}
}
