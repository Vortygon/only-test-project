<?php

class Router {
  private $routes = [];
  private $pagesDir;

  public function __construct($pagesDir = "./pages/") {
    $this->pagesDir = $pagesDir;
    $this->addRoute("/404", $pagesDir . "404.php");
  }

  public function addRoute($path, $callback) {
    $this->routes[$path] = $callback;
  }

  public function getRoutes() {
    return $this->routes;
  }

  public function resolve($path) {
    if (isset($this->routes[$path])) {
      include $this->routes[$path];
    } else {
      include $this->routes["/404"];
    }
  }
}

?>