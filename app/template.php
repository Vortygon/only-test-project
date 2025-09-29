<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon.png" type="image/png">
    <title>Главная страница</title>
    <link rel="stylesheet" href="./pages/style.css" type="text/css">
    <style><?php include "./pages/style.css" ?></style>
  </head>
  <body>
    <header>
      <a class="logo" href="/">Only.Auth</a>
      <a href="/register" class="button">Регистрация</a>
      <a href="/login" class="button">Вход</a>
    </header>
    <main>
      <?php
        $router->resolve($request);
      ?>
    </main>
  </body>
</html>