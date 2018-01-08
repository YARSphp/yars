<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;


interface Options {
	public function options(Request $request, Response $response, $args);
}