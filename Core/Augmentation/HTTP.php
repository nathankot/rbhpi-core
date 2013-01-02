<?php

namespace Core\Augmentation;

use Core\Prototype\Request;

trait HTTP
{
	protected function getRequest()
	{
		$request_uri = trim($_SERVER['REQUEST_URI'], '/');

		if ($request_uri === '__RBHP_MAKE_REQUEST') {
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

	protected function injectRoute($host, Request $request)
	{
		$host = ltrim($host, 'http:');
		$host = trim($host, '/');
		$host = "http://{$host}";
		$url = "{$host}/__RBHP_MAKE_REQUEST";
		$data = serialize($request);
		$headers = [
				'Content-type' => 'text/x-rbhprequest'
		];
		$response = \Requests::post($url, $headers, $data, $options);
		return unserialize($response->body);
	}
}
