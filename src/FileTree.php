<?php

namespace Yars;

use Yars\methods\Delete;
use Yars\methods\Get;
use Yars\methods\Post;
use Yars\methods\Put;

class FileTree {
	private $name;
	private $origName;
	private $children = [];
	private $static   = true;
	private $endpoint = true;
	private $methods  = [];

	public function addChild($name) {
		$this->children[] = $name;
	}

	/**
	 * @param $name
	 *
	 * @return \Yars\FileTree|null
	 */
	public function findChild($name) {
		foreach ($this->children as $child) {
			if ($child->name == $name) {
				return $child;
			}
		}

		return NULL;
	}

	/**
	 * @param $files
	 * @param $basedir
	 *
	 * @return \Yars\FileTree
	 */
	public static function readIn($files, $data) {
		$basedir    = $data["dir"];
		$root       = new FileTree();
		$root->name = "";
		foreach ($files as $file) {
			$file = str_replace(".php", "", $file);
			$file = ltrim(explode($basedir, $file)[1], "/");
			$ex   = explode("/", $file);

			$name  = $ex[count($ex) - 1];
			$paths = $ex;
			unset($paths[count($ex) - 1]);

			$node = self::buildNode($name, true);

			$ns    = $data["namespace"] . "\\" . str_replace("/", "\\", $file);
			$class = new $ns();
			if ($class instanceof Get) {
				$node->methods[] = "get";
			}
			if ($class instanceof Post) {
				$node->methods[] = "post";
			}
			if ($class instanceof Put) {
				$node->methods[] = "put";
			}
			if ($class instanceof Delete) {
				$node->methods[] = "delete";
			}
			$current = $root;

			foreach ($paths as $path) {
				$c = $current->findChild($path);
				if ($c == NULL) {
					$n = self::buildNode($path);
					$current->addChild($n);
					$current = $n;
				} else {
					$current = $c;
				}
			}
			$current->addChild($node);
		}

		return $root;
	}

	private static function buildNode(string $name, bool $endpoint = false) {
		$node           = new FileTree();
		$node->origName = $name;
		if (substr($name, 0, 1) == "_" && substr($name, 1, 1) != "_") {
			$node->static = false;
			$node->name   = substr($name, 1);
		} else {
			if (substr($name, 0, 2) == "__") {
				$node->static = true;
				$node->name   = substr($name, 1);
			} else {
				$node->static = true;
				$node->name   = $name;
			}
		}
		$node->endpoint = $endpoint;

		return $node;
	}

	public function getName() {
		return $this->name;
	}

	/**
	 * @return \Yars\FileTree[]
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @return bool
	 */
	public function isEndpoint(): bool {
		return $this->endpoint;
	}

	/**
	 * @return bool
	 */
	public function isStatic(): bool {
		return $this->static;
	}

	/**
	 * @return string
	 */
	public function getOrigName(): string {
		return $this->origName;
	}

	/**
	 * @return string[]
	 */
	public function getMethods(): array {
		return $this->methods;
	}

}