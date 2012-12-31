<?php
/**
 * @version 0.1.0
 */

namespace Core\Wireframe\Blueprint;

/**
 * The View should take data, and be able to render it into a variety of different views.
 */
interface View
{
	public $data;
	public $template;
	public function init($data);
	public function setTemplate($template);
	public function getData();
	public function toJSON();
	public function toHTML();
}
