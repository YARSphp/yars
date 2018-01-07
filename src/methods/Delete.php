<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;

interface Delete {
	public function delete(Request $request, Response $response, $args);
}