<?php

// Яндекс Капча

define("SMARTCAPTCHA_SERVER_KEY", "t2UyqtchrCVnCqaSicmBJzVEQ54VAw");

function check_captcha($token) {
    $ch = curl_init();
    $args = http_build_query([
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'], 
    ]);

    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        // echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }

    $resp = json_decode($server_output);
    return $resp->status === "ok";
}

function checkCaptchaComletion() {
    $captcha_token = $_POST["smart-token"] ?? "";
    if (empty($captcha_token)) {
        return false;
    }
    return check_captcha($captcha_token);
}
