<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;


interface Any {
	public function any(Request $request, Response $response, $args);
}