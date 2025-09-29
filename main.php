<?php

session_start();

$pagesDir = __DIR__ . "/pages/";
$appDir = __DIR__ . "/app/";

require $appDir . "/router.php";
$router = new Router($pagesDir);
$request = $_SERVER["REQUEST_URI"];
$router->addRoute("/",  $pagesDir . "index.php");

// $router->resolve($request);
include $appDir . "template.php";


?>