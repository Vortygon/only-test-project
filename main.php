<?php

session_start();

$pagesDir = __DIR__ . "/pages/";

include "./router.php";
$router = new Router();
$request = $_SERVER["REQUEST_URI"];
$router->addRoute("/",  $pagesDir . "index.php");

// $router->resolve($request);
include $pagesDir . "template.php";


?>