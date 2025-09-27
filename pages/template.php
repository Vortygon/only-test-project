<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon.png" type="image/png">
    <title>Главная страница</title>
    <link rel="stylesheet" href="./pages/style.css">
    <style><?php include("./pages/style.css") ?></style>
  </head>
  <body>
    <header>
      <strong>Only.Auth</strong>
      <button type="button">Регистрация</button>
      <button type="button">Вход</button>
    </header>
    <main>
      <?php
        include("index.php");
      ?>
    </main>
  </body>
</html>