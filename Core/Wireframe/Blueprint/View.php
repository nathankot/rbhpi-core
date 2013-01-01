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
	public function init($data);
	public function setTemplate($template);
	public function getTemplate();
	public function setLayout($layout);
	public function getLayout();
	public function getData();
	public function toJSON();
	public function toHTML();
}
