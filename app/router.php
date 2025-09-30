<?php

class Router {
  private $routes = [];
  private $apiRoutes = [];
  private $pagesDir;
  private $publicDir;
  private $templateDir;
  private $componentsDir;

  public function __construct($workDir = "../", $templateDir = "../app/template.php") {
    $this->pagesDir = $workDir . "/pages/";
    $this->publicDir = $workDir . "/public/";
    $this->templateDir = $templateDir;
    $this->componentsDir = $workDir . "/app/components/";
    $this->addRoute("GET", "/404", fn() => $this->renderPage("/404"));
    $this->scanPagesDir();
  }

  public function addRoute($method, $path, $callback) {
    // $this->routes[$method][$path] = $callback;
    $this->routes[] = [
      "method" => $method,
      "path" => $path,
      "callback" => $callback,
    ];
  }

  public function addApiRoute($method, $endpoint, $callback) {
    $this->apiRoutes[] = [
      "method" => $method,
      "endpoint" => $endpoint,
      "callback" => $callback,
    ];
  }

  public function getRoutes() {
    return $this->routes;
  }

  // Сканирование папки /pages/ и создание путей к страницам
  private function scanPagesDir($path = "") {
    $files = scandir($this->pagesDir . $path);
    
    foreach ($files as $file) {
      if ($file == "." || $file == "..") continue; 
      
      $filePath = $this->pagesDir . $path . "/" . $file;
      // echo $filePath;

      if (is_dir($filePath)) {
        $this->scanPagesDir($path . $file);
      }
      
      if (is_file($filePath)) {
        $this->addRoute("GET", "/" . $path . "/" . pathinfo($filePath, PATHINFO_FILENAME), fn() => $this->renderPage("/" . $path . "/" . pathinfo($filePath, PATHINFO_FILENAME)));
      }
    }
  }

  public function resolve() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    // Обработка запросов к API
    if ($this->handleApi($method, $uri)) {
      return;
    }

    // Обработка страниц
    if ($this->handlePage($method, $uri)) {
      return;
    }

    // Обработка статических файлов
    if ($this->servePublicFile($uri)) {
      return;
    }

    // Путь/файл не найден
    http_response_code(404);
    $this->handlePage("GET", "/404");
    exit;
  }

  private function servePublicFile($path) {
    // Путь к /public/
    $publicDir = $this->publicDir . $path;

    /*
    Обрабатываем директорию /public/ .
    Все файлы в директории /public/ должны быть общедоступны через запросы по пути / .
    Файлы возвращаются "такими, какие есть" 
    */
    if ($path !== "/" && file_exists($publicDir) && is_file($publicDir)) {
      header("Content-Type: " . getMimeType($publicDir));
      readfile($publicDir);
      return true;
    } 

    return false;
  }

  public function renderPage($path) {
    ob_start();
    include $this->pagesDir . $path . ".php";
    $componentsDir =  $this->componentsDir;
    $content = ob_get_clean();
    
    if (isset($this->templateDir)) {
      include $this->templateDir;
    } else {
      include $content;
    }
  }

  private function handlePage($method, $uri) {
    foreach ($this->routes as $route) {
      if ($route['method'] == $method && $route['path'] == $uri) {
        call_user_func($route['callback']);
        return true;
      }
    }
    return false;
  }

  private function handleApi($method, $uri) {
    if (strpos($uri, '/api/') !== 0) return false;
    foreach ($this->apiRoutes as $route) {
      if ($route['method'] == $method && $route['endpoint'] == $uri) {
        header('Content-Type: application/json');
        echo json_encode(call_user_func($route['callback']));
        return true;
      }
    }
    return false;
  }

}

// Получаем MIME-тип содержимого файла. (mime_content_type() не доступен)
function getMimeType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $map = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'html' => 'text/html',
        'txt'  => 'text/plain',
        'pdf'  => 'application/pdf',
        'default' => 'application/octet-stream'
    ];
    return $map[$ext] ?? $map['default'];
}

?>