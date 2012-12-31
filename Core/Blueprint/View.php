<?php

namespace Core\Blueprint;

class View extends \Core\Blueprint\Object implements
	\Core\Wireframe\Blueprint\View
{
	use \Core\Augmentation\Adaptable;

	public $data;

	public $template;

	public function init($data)
	{

	}

	public function toJSON()
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}

	public function toHTML()
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}
}
