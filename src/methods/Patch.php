<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;


interface Patch {
	public function patch(Request $request, Response $response, $args);
}