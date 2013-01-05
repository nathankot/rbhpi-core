<?php
/**
 * @version 1.0.0
 */

namespace Core\Wireframe\Prototype;

/**
 * Request interface. The request is responsible for taking an a variety of paths and breaking them down.
 */
interface Request
{
	public function init($request_components = [
			'path' => null
		,	'payload' => null
		,	'host' => null
		,	'method' => null
		,	'format' => null
	]);
	public function getFormat();
	public function getComponents();
	public function getPath();
	public function getMethod();
	public function getHost();
	public function getPayload();
	public function injectTo($host = null);
	public function inject();
}
