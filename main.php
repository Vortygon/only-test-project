<?php

session_start();

// Директории
$pagesDir = __DIR__ . "/pages/";
$appDir = __DIR__ . "/app/";
$publicDir = __DIR__ . "/public/";
$componentsDir = $appDir . "/components/";
$templateDir = $appDir . "/template.php";

// Роутер
require $appDir . "router.php";
$router = new Router(__DIR__, $templateDir);
$request = $_SERVER["REQUEST_URI"];
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Пути
$router->addRoute("GET", "/",  fn() => $router->renderPage("index")); // Главная страница

// API
$router->addApiRoute("GET", "/api/test", function () use ($router) {
  return $router->getRoutes();
});

$router->resolve();

?>