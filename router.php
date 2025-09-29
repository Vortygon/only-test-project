<?php

// $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// if ($uri === '/') {
//   include './pages/template.php';
// }
// else {
//   include './pages/404.php';
//   echo '404';
// }

// class Router {
//   private $routes = [];
//   private $pagesDir;
//   private $baseUrl;

//   public function __construct($pagesDir = 'pages', $baseUrl = '') {
//     $this->pagesDir = rtrim($pagesDir,'/');
//     $this->baseUrl = rtrim($this->baseUrl,'/');
//     $this->parsePagesDirectory("./pages/");
//   }

//   public function addRoute($path, $callback) {
//     $this->routes[$path] = $callback;
//   }

//   public function getRoutes() {
//     return $this->routes;
//   }

//   public function dispatch($path) {
//     if (isset($this->routes[$path])) {
//       return call_user_func($this->routes[$path]);
//     } else return $this->dispatch("/404");
//   }

//   private function parsePagesDirectory($dir) {
//     $files = scandir($dir);

//     foreach ($files as $file) {
//       if ($file == "." || $file == "..") continue;

//       $fullDir = $this->pagesDir . "/" . $file;
      
//       if (is_dir($fullDir)) {
//         $this->parsePagesDirectory($fullDir);
//       } elseif (is_file($fullDir)) {
//         $this->addRoute($fullDir, function ($dir) use ($fullDir) {
//           return include $dir;
//         });
//       }
//     }
//   } 
// }

// $router = new Router();

// // $router->addRoute("/",function() {
// //   return include "./pages/template.php";
// // });

// // $router->addRoute("/404",function() use (&$router) {
// //   return "<h1>" .
// //     implode(', ', $router->getRoutes())
// //   . "</h1>";
// //   // return include "./pages/404.php";
// // });

// if($_SERVER["REQUEST_METHOD"] == "GET") {
//   // $path = $_GET["path"];
//   $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
//   echo $router->dispatch($path);
//   exit;
// }

class Router {
  private $routes = [];

  public function __construct() {
    $this->addRoute('/404',  __DIR__ . '/pages/404.php');
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
      include $this->routes['/404'];
    }
  }
}

?>