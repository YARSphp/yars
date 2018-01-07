<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;

interface Put {
	public function put(Request $request, Response $response, $args);
}