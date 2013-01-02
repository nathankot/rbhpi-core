<?php

namespace Core\Augmentation;

use Core\Prototype\Request;

trait HTTP
{
	protected function getRequest()
	{
		$request_uri = trim($_SERVER['REQUEST_URI'], '/');

		if ($request_uri === self::$config['rbhp_injection_token']) {
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
			$method = $_SERVER['REQUEST_METHOD'] ?: self::$config['default_method'];
			$format = isset($_SERVER['HTTP_ACCEPT']) ? \Bitworking\Mimeparse::bestMatch(self::$config['available_formats'], $_SERVER['HTTP_ACCEPT']) : null;
		}

		return [
				'path' => $path
			,	'payload' => $payload
			,	'host' =>	'localhost'
			,	'method' => $method
			,	'format' => $format
		];
	}

	protected function injectRoute(Request $request)
	{
		$host = $request->getHost();
		$host = str_replace('http://', '', $host);
		$host = rtrim($host, '/');
		$host = "http://{$host}";
		$url = "{$host}/".self::$config['rbhp_injection_token'];
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
