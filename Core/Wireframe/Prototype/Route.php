<?php
/**
 * @version 0.1.0
 */

namespace Core\Wireframe;

use Core\Prototype\Request;

/**
 * The route object needs to be able to break a route down into interpretable components.
 */
interface Route
{
	public static function connect($route, $handle = false);
	public function init(Request $request);
	public function getController();
	public function getMethod();
	public function getArgs();
	public function getFormat();
}
