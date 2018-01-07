<?php

namespace Yars;

use Yars\middlewares\Encoding;
use Yars\middlewares\Jsonp;
use Yars\middlewares\TrailingSlash;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\App as Application;
use Slim\Container;

class Server {
	private $cacheFile;
	private $cache = true;
	private $data  = [];
	private $application;
	private $slash = true;
	/**
	 * @var \Yars\CacheBuilder
	 */
	private $cacheBuilder = NULL;

	public function __construct() {
		$c                    = new Container();
		$c['notFoundHandler'] = Errors::c404($c);
		$this->application    = new Application($c);
		$this->cacheFile      = getcwd() . "/.cache/cache.php";
	}

	public function addSlug($slug, $namespace, $dir) {
		$this->data[$slug] = [
			"slug"      => $slug,
			"namespace" => $namespace,
			"dir"       => $dir
		];
	}

	public function useCache($cache) {
		$this->cache = $cache;
	}

	public function run() {
		$this->application->add(new Encoding());
		$this->application->add(new TrailingSlash($this->slash));
		$this->application->add(new Jsonp());

		$this->cacheBuilder = new CacheBuilder($this->cacheFile);

		if (!is_file($this->cacheFile) || !$this->cache) {
			foreach ($this->data as $slug => $item) {
				$files    = $this->getFilesByDir($item["dir"]);
				$fileTree = FileTree::readIn($files, $item);
				$this->buildStack($fileTree, $item["namespace"], $item["slug"]);
				$this->cacheBuilder->build();
			}
		} else {
			include $this->cacheFile;
		}
		try {
			$this->application->run();

			/*$routes = $this->application->getContainer()->router->getRoutes();


			foreach ($routes as $route) {
			echo $route->getPattern(), "<br>";
			}*/
		} catch (Exception $e) {
			echo $e->getTraceAsString();
		}

	}

	private function getFilesByDir($path) {
		$files = [];
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
			if (!$file->isDir()) {
				$files[] = $file->getPathname();
			}
		}

		return $files;
	}

	public function trailingSlash($true) {
		$this->slash = $true;
	}


	private function buildStack(FileTree $fileTree, $namespace, $parent) {

		if (!empty($fileTree->getName())) {
			$namespace = $namespace . "\\" . $fileTree->getOrigName();
			$parent    = $parent . "/" . ($fileTree->isStatic() ? $fileTree->getName() : "{" . $fileTree->getName() . "}");
			if ($fileTree->isEndpoint()) {
				foreach ($fileTree->getMethods() as $method) {
					$path = rtrim($parent, "/") . ($this->slash ? '/' : '');
					$ns   = $namespace . ':' . $method;
					$this->application->$method($path, $ns);
					$this->cacheBuilder->add($method, $path, $ns);
				}
			}
		}
		if (!empty($fileTree->getChildren())) {
			foreach ($fileTree->getChildren() as $child) {
				$this->buildStack($child, $namespace, $parent);
			}
		}
	}
}