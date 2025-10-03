<?php

require_once __DIR__ . "/../auth/session_helper.php";

$user = SessionHelper::getUser();
$name = $user["username"] ?? null;

?>

<header>
  <a class="logo" href="/">Only.Auth</a>
  <?php if ($user) {
    echo <<<HTML
      Пользователь: $name
      <form method="get" action="/auth/logout">
        <input type="submit" value="Выход" class="button">
      </form>
    HTML;
  } else {
    echo <<<HTML
      <a href="/auth/register" class="button">Регистрация</a>
      <a href="/auth/login" class="button">Вход</a>
    HTML;
  } 
  ?>
  
</header>