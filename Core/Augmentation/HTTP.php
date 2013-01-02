<?php

namespace Core\Augmentation;

use Core\Prototype\Request;

trait HTTP
{
	protected function getRequest()
	{
		return [
				'uri' => $_SERVER['REQUEST_URI']
			,	'payload' => $_POST ?: json_decode(file_get_contents('php://input'), true)
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
