<?php

namespace Yars\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware to add or remove the trailing slash.
 */
class TrailingSlash {

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
		$uri  = $request->getUri();
		$path = $uri->getPath();

		if (strlen($path) > 1) {
			if ($this->addSlash) {
				if (substr($path, -1) !== '/' && !pathinfo($path, PATHINFO_EXTENSION)) {
					$path .= '/';
				}
			} else {
				$path = rtrim($path, '/');
			}
		} elseif ($path === '') {
			$path = '/';
		}
		if ($path != $uri->getPath()) {
			return $response->withStatus(302)->withHeader('Location', "/" . ltrim($path, '/'));
		} else {
			return $next($request->withUri($uri->withPath($path)), $response);
		}
	}
}