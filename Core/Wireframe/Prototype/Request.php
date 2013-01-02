<?php
/**
 * @version 0.2.0
 */

namespace Core\Wireframe\Prototype;

/**
 * Request interface. The request is responsible for taking an a variety of paths and breaking them down.
 */
interface Request
{
	public function init($path);
	public function getFormat();
	public function getComponents();
	public function getPath();
	public function getMethod();
	public function getHost();
}
