<?php

// Работа с токенами
class JWT {
  public static function encode($payload) {
    $header = json_encode(["typ" => "JWT", "alg" => "HS256"]);
    $payload = json_encode($payload);

    $base64Header = self::base64Encode($header);
    $base64Payload = self::base64Encode($payload);

    $signature = hash_hmac(
      "sha256",
      $base64Header . "." . $base64Payload,
      Config::JWT_SECRET,
      true
    );

    $base64Signature = self::base64Encode($signature);

    return $base64Header .".". $base64Payload .".". $base64Signature;
  }

  public static function decode($jwt) {
    $data = explode(".", $jwt);

    if (count($data) != 3) {
      throw new Exception("Неверный токен");
    }

    list($base64Header, $base64Payload, $base64Signature) = $data;

    $signature  = self::base64Decode($base64Signature);

    $signatureCheck = hash_hmac(
      "sha256",
      $base64Header . "." . $base64Payload,
      Config::JWT_SECRET,
      true
    );

    if (!hash_equals($signature, $signatureCheck)) {
      throw new Exception("Неверная сигнатура");
    }

    $payload = json_decode(self::base64Decode($base64Payload), true);

    if (isset($payload["exp"]) && $payload['exp'] < time()) {
      throw new Exception("Срок действия токена истёк");
    }

    return $payload;
  }

  private static function base64Encode($data) {
    return rtrim(strtr(base64_encode($data),"+/","-_"), "=");
  }

  public static function base64Decode($data) {
    return base64_decode(strtr($data,"-_","+/"));
  }
}