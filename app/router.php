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
    // $this->scanPagesDir();
  }

  public function addRoute($method, $path, $protected, $callback) {
    foreach ($this->routes as $route) {
      if ($route["method"] == $method && $route["path"] == $path) return;
    }
    $this->routes[] = [
      "method" => $method,
      "path" => $path,
      "protected" => $protected,
      "callback" => $callback,
    ];
  }

  public function addApiRoute($method, $endpoint, $protected, $callback) {
    $this->apiRoutes[] = [
      "method" => $method,
      "endpoint" => $endpoint,
      "protected" => $protected,
      "callback" => $callback,
    ];
  }

  public function getRoutes() {
    return $this->routes;
  }

  public function getApiRoutes() {
    return $this->apiRoutes;
  }

  // Сканирование папки /pages/ и создание путей к страницам
  public function scanPagesDir($path = "") {
    $files = scandir($this->pagesDir . $path);
    
    foreach ($files as $file) {
      if ($file == "." || $file == "..") continue; 
      
      $filePath = $this->pagesDir . $path . "/" . $file;
      // echo $filePath;

      if (is_dir($filePath)) {
        $this->scanPagesDir($path . $file);
      }
      
      if (is_file($filePath)) {
        $finalDir = $path . "/" . pathinfo($filePath, PATHINFO_FILENAME);
        if (strpos($finalDir, "/") !== 0) $finalDir = "/" . $finalDir;
        $this->addRoute("GET", $finalDir, false, fn() => $this->renderPage($finalDir));
        $this->addRoute("POST", $finalDir, false, fn() => $this->renderPage($finalDir));
      }
    }
  }

  public function resolve() {
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $method = $_SERVER["REQUEST_METHOD"];

    // Настройка CORS
    // if ($method === "OPTIONS") {
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        // header("Access-Control-Allow-Headers: Content-Type, Authorization");
        // http_response_code(200);
        // exit;
    // }

    // Обработка запросов к API
    if ($this->handleApi($method, $uri)) {
      header("Access-Control-Allow-Origin: *");
      header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
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

  // Отрисовка страницы
  public function renderPage($path) {
    ob_start();
    $componentsDir =  $this->componentsDir;
    include $this->pagesDir . $path . ".php";
    $content = ob_get_clean();
    
    if (isset($this->templateDir)) {
      include $this->templateDir;
    } else {
      include $content;
    }
  }

  // Обработка страниц
  private function handlePage($method, $uri) {
    foreach ($this->routes as $route) {
      if ($route["method"] == $method && $route["path"] == $uri) {
        if ($route["protected"]) {
          $auth = new Auth();
          $user = $auth->check();
          if (!$user) {
            http_response_code(401);
            header("Location: /");
            exit;
          }
        }
        call_user_func($route["callback"]);
        return true;
      }
    }
    return false;
  }

  // Обработка запросов API
  private function handleApi($method, $uri) {
    if (strpos($uri, "/api/") !== 0) return false;
    foreach ($this->apiRoutes as $route) {
      if ($route["method"] == $method && $route["endpoint"] == $uri) {
        header("Content-Type: application/json");

        try {
          if ($route["protected"]) {
            $auth = new Auth();
            $user = $auth->verifyToken();
            $result = call_user_func($route["callback"]);
          } else {
            $result = call_user_func($route["callback"]);
          }
          echo json_encode($result);
        } catch (Exception $e) {
          http_response_code(401);
          echo json_encode(["error"=> $e->getMessage()]);
        }

        return true;
      }
    }
    return false;
  }

  // Проверка авторизации
  private function checkAuth() {
    require_once __DIR__ . "/auth/session_helper.php";
    return SessionHelper::getUser() ? true : false;
  }

  // Выполнить внутренний запрос к API
  public function requestInternalApi($method, $uri) {
    ob_start();
    $this->handleApi($method, $uri);
    $content = ob_get_clean();
    return $content;
  }

}

// Получаем MIME-тип содержимого файла
function getMimeType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $map = [
        "css"  => "text/css",
        "js"   => "application/javascript",
        "json" => "application/json",
        "jpg"  => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png"  => "image/png",
        "gif"  => "image/gif",
        "svg"  => "image/svg+xml",
        "ico"  => "image/x-icon",
        "html" => "text/html",
        "txt"  => "text/plain",
        "pdf"  => "application/pdf",
        "default" => "application/octet-stream"
    ];
    return $map[$ext] ?? $map["default"];
}