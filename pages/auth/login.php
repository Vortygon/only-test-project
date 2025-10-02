<?php

require_once($componentsDir ."../auth/session_helper.php");

SessionHelper::start();

if (SessionHelper::getUser()) {
    header("Location: /profile");
    exit;
}

$login = $_GET["login"] ?? null;
$message = null;

if ($_SESSION["message"] ?? null) {
  $message = $_SESSION["message"];
  $_SESSION["message"] = null;
}

$flash = SessionHelper::getFlash();
if ($flash) {
  $message = $flash["message"];
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
        Логин:
        <input type="text" name="login" id="login_input" value="<?php echo $login ?>" required>
      </li>
      <li>
        Пароль: 
        <input type="password" name="password" id="password_input" required>
      </li>
    </ul>

    <div 
      style="height: 100px"
      id="captcha-container"
      class="smart-captcha"
      data-sitekey="ysc1_q5aWEuEcpfjwI8fhRsvqqmrY4mlZM0vBT96HBEmU41387a80"
    ></div>

    <input type="submit" value="Вход" class="button highlight">

</form>

</div>