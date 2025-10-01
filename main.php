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
$router->addRoute("GET", "/", false, function () use ($router) {
  $router->renderPage("index");
});

// API
$router->addApiRoute("GET", "/api/routes", false, function () use ($router) {
  return $router->getRoutes();
});
$router->addApiRoute("GET", "/api/api_routes", false, function () use ($router) {
  return $router->getApiRoutes();
});

$router->resolve();

?>