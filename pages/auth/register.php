<?php

require_once $componentsDir ."../auth/session_helper.php";

SessionHelper::start();

if (SessionHelper::getUser()) {
    header("Location: /profile");
    exit;
}

$username = $_GET["username"] ?? null;
$email = $_GET["email"] ?? null;
$phone = $_GET["phone"] ?? null;
$message = null;

$flash = SessionHelper::getFlash();
if ($flash && $flash["type"] == "fail") {
  $message = $flash["message"];
}

if ($_SESSION["message"] ?? null) {
  $message = $_SESSION["message"];
  $_SESSION["message"] = null;
}

?>

<head>
  <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<div class="fullpage">

  <form method="post" class="panel">
    <?php
      if (isset($message)) {
        echo "<div class='message'>" . $message . "</div>";
      }
    ?>
    <ul>
      <li>
        Имя:
        <input type="text" name="username" id="name_input" value="<?php echo $username ?>" required>
      </li>
      <li>
        Email:
        <input type="email" name="email" id="email_input" value="<?php echo $email ?>" required>
      </li>
      <li>
        Телефон:
        <input type="tel" name="phone" id="phone_input" value="<?php echo $phone ?>" pattern = "+7 [0-9]{3} [0-9]{3}-[0-9]{4}" required>
      </li>
      <li>
        Пароль: 
        <input type="password" name="password" id="password_input" required>
      </li>
      <li>
        Подтверждение пароля: 
        <input type="password" name="passwordCheck" id="password_check_input" required>
      </li>
    </ul>

    <div 
      style="height: 100px"
      id="captcha-container"
      class="smart-captcha"
      data-sitekey="ysc1_q5aWEuEcpfjwI8fhRsvqqmrY4mlZM0vBT96HBEmU41387a80"
    ></div>

    <input type="submit" value="Регистрация" class="button highlight">

</form>

</div>