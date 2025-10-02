<?php

$lifetime = 60 * 60 * 24 * 30; // Храним куки сессии 30 дней
session_set_cookie_params($lifetime, "/");
session_start();

// Директории
$pagesDir = __DIR__ . "/pages/";
$appDir = __DIR__ . "/app/";
$publicDir = __DIR__ . "/public/";
$componentsDir = $appDir . "/components/";
$templateDir = $appDir . "/template.php";

// Зависимости
require_once $appDir . "router.php"; 
require_once $appDir . "routes.php"; 
require_once $appDir . "auth/auth.php";
require_once $appDir . "auth/storage.php";
require_once $appDir . "auth/jwt.php";
require_once $appDir . "auth/config.php";

// Роутер
$router = new Router(__DIR__, $templateDir);
registerRoutes($router);
$router->resolve();