<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/public/img/favicon.png" type="image/png">
    <title>Главная страница</title>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
  </head>
  <body>
    <?php include $componentsDir . "header.php" ?>  
    <main>
      <?=
        $content;
      ?>
    </main>
  </body>
</html>