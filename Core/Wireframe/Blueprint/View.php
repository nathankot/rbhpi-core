<?php
/**
 * @version 0.1.0
 */

namespace Core\Wireframe\Blueprint;

interface View
{
	public $data;
	public $template;
	public function init($data);
	public function toJSON();
	public function toHTML();
}
