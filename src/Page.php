<?php

namespace Yars;

abstract class Page {
	protected $error;

	public function __construct() {
		$this->error = new Errors();
	}
}