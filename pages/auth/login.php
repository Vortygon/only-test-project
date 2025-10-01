<?php



?>

<head>
  <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<div class="fullpage">

  <form action="/api/login" method="post" class="panel">

    <ul>
      <li>
        Логин:
          <input type="text" name="name" id="name_input" required>
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