<?php
/**
 * @version 0.1.0
 */

namespace Core\Augmentation;

use Core\Prototype\Request;

/**
 * Provide a set of strategies of HTTP interaction. These should be specific and highly abstracted.
 * Lower-abstraction requests can be made by using the `\Requests` class. (Which this Augmentation also makes use of.)
 */
trait HTTP
{
	/**
	 * Obtain components of the request that face this instance of RBHP. Takes into account injection,
	 * in which case it uses request components obtained from the injected `\Core\Prototype\Request` Object.
	 * @return array An array of current request components.
	 */
	protected function getRequest()
	{
		$request_uri = trim($_SERVER['REQUEST_URI'], '/');

		if ($request_uri === Request::$config['rbhp_injection_token']) {
			if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
				throw new \Exception\BadRequest("RBHP Injections can only be accomplished with POST.");
			}
			$object = unserialize(file_get_contents('php://input'));
			$path = $object->getPath();
			$payload = $object->getPayload();
			$method = $object->getMethod();
			$format = $object->getFormat();
		} else {
			$path = empty($request_uri) ? null : "/{$request_uri}";
			$payload = $_POST ?: json_decode(file_get_contents('php://input'), true);
			$method = $_SERVER['REQUEST_METHOD'] ?: Request::$config['default_method'];
			$format = isset($_SERVER['HTTP_ACCEPT']) ? \Bitworking\Mimeparse::bestMatch(Request::$config['available_formats'], $_SERVER['HTTP_ACCEPT']) : null;
		}

		return [
				'path' => $path
			,	'payload' => $payload
			,	'host' =>	'localhost'
			,	'method' => $method
			,	'format' => $format
		];
	}

	/**
	 * Inject the given request object into another server, and obtain its response.
	 * @param  Request $request The Request object that is to be injected.
	 * @return mixed           The response from the server, any serialized object will be unserialized.
	 */
	protected function injectRoute(Request $request)
	{
		$host = $request->getHost();
		$host = str_replace('http://', '', $host);
		$host = rtrim($host, '/');
		$host = "http://{$host}";
		$url = "{$host}/".Request::$config['rbhp_injection_token'];
		$data = serialize($request);
		$headers = [
				'Content-type' => 'text/x-rbhprequest'
		];
		$response = \Requests::post($url, $headers, $data, $options);
		$data = unserialize($response->body);
		if ($data === false) {
			$data = $response->body;
		}
		return $data;
	}
}
