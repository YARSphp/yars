<?php

namespace Yars\methods;

use Slim\Http\Response;
use Slim\Http\Request;


interface Post {
	public function post(Request $request, Response $response, $args);
}