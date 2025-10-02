<?php

require_once $componentsDir ."../auth/session_helper.php";

SessionHelper::start();

$message = null;
$user = SessionHelper::getUser();
$user_name = $user["username"];
$user_email = $user["email"];
$user_phone = $user["phone"];

if ($_SESSION["message"] ?? null) {
  $message = $_SESSION["message"];
  $_SESSION["message"] = null;
}

$flash = SessionHelper::getFlash();
if ($flash && $flash["type"] == "fail") {
  $message = $flash["message"];
}

?>

<div class="fullpage">

  <form method="post" class="panel">
    <?php
      if (isset($message)) {
        echo "<div class='message'>" . $message . "</div>";
      }
    ?>
    <ul class="info">
      <li>
        Имя: <div><?php echo $user_name ?></div>
      </li>
      <li>
        Email: <div><?php echo $user_email ?></div>
      </li>
      <li>
        Телефон: <div><?php echo $user_phone ?></div>
      </li>
    </ul>
    <ul>
      <li>
        Новое имя:
        <input type="text" name="username" id="name_input">
      </li>
      <li>
        Новый email:
        <input type="email" name="email" id="email_input">
      </li>
      <li>
        Новый телефон:
        <input type="tel" name="phone" id="phone_input" pattern = "+7 [0-9]{3} [0-9]{3}-[0-9]{4}">
      </li>
      <li>
        Новый пароль: 
        <input type="password" name="newPassword" id="new_password_input">
      </li>
      <li style="margin-top: 1.5rem;">
        Введите пароль: 
        <input type="password" name="password" id="password_input" required>
      </li>
    </ul>
    <input type="submit" value="Обновить данные" class="button highlight">

  </form>

</div>