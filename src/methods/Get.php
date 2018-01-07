<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;

interface Get {
	public function get(Request $request, Response $response, $args);
}