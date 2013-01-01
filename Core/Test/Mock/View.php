<?php

namespace Core\Test\Mock;

class View extends \Core\Blueprint\View
{
	public function listNumbers()
	{
		return implode('-', $this->data['numbers']);
	}
}
