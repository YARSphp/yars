<?php

namespace Yars\middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class Jsonp {
	/**
	 * @var string
	 */
	protected $callbackKey;

	/**
	 * @var string|null
	 */
	protected $callbackName;

	/**
	 * Create Jsonp.
	 *
	 * @param string $callbackKey
	 */
	public function __construct($callbackKey = 'callback') {
		$this->callbackKey = $callbackKey;
	}

	/**
	 * Build Response with the callback.
	 *
	 * @param Response $response
	 *
	 * @return Response
	 */
	protected function buildJsonpResponse(Response $response) {
		$content     = (string)$response->getBody();
		$contentType = $response->getHeaderLine('Content-Type');

		if (strpos($contentType, 'application/json') === false) {
			$content = '"' . $content . '"';
		}

		$callback = "{$this->callbackName}({$content});";

		$newResponse = new Response(200);
		$newResponse->getBody()->write($callback);

		return $newResponse->withHeader('Content-Type', 'application/javascript');
	}

	/**
	 * Invoke middleware.
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @param callable $next
	 *
	 * @return Response
	 */
	public function __invoke(Request $request, Response $response, callable $next) {
		$param = $request->getQueryParam($this->callbackKey);

		if (is_string($param) && !empty($param)) {
			$this->callbackName = $param;
		}

		/* @var $newResponse Response */
		$newResponse = $next($request, $response);

		if ($this->callbackName) {
			$newResponse = $this->buildJsonpResponse($newResponse);
		}

		return $newResponse;
	}
}
