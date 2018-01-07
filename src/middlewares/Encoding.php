<?php

namespace Yars\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware to add or remove the trailing slash.
 */
class Encoding {

	/**
	 * @var bool Add or remove the slash
	 */
	private $addSlash;

	/**
	 * Configure whether add or remove the slash.
	 *
	 * @param bool $addSlash
	 */
	public function __construct(bool $addSlash = false) {
		$this->addSlash = $addSlash;
	}

	/**
	 * Execute the middleware.
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 * @param callable               $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
		/* @var $newResponse ResponseInterface */
		$response->withHeader('Content-Type', 'application/json;charset=utf-8');
		$newResponse = $next($request, $response);



		return $newResponse;
	}
}