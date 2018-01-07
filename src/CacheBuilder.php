<?php

namespace Yars;


class CacheBuilder {
	private $content = "<?php /* DO NOT MODIFY */";
	private $file;

	public function __construct($file) {
		$this->file = $file;
	}

	public function add($method, $path, $namespace) {
		$this->content .= '$this->application->' . $method . '(\'' . $path . '\',\'' . $namespace . '\');';
	}

	public function build() {
		if (!is_dir(dirname($this->file))) {
			mkdir(dirname($this->file));
		}
		file_put_contents($this->file, $this->content);
	}
}