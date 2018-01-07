<?php

namespace Yars;


use Slim\Http\Response;

class Errors {
	public static function c404($c) {
		return function () use ($c) {
			return function ($request, $response) use ($c) {
				return $c['response']
					->withStatus(404)
					->withJson(["error" => 404, "message" => "Page not found"])
					->withHeader('X-Powered-By', 'alindner/rest:1.0.0');
			};
		};
	}

	/**
	 * @param \Slim\Http\Response $response
	 * @param    string                 $message
	 *
	 * @return \Slim\Http\Response
	 */
	public static function e404(Response $response, $message) {
		return $response
			->withStatus(404)
			->withJson(["error" => 404, "message" => $message])
			->withHeader('X-Powered-By', 'alindner/rest:1.0.0');
	}
}