<?php

// Реализация авторизации
class Auth {
  private $storage;

  public function __construct() {
    $this->storage = new Storage();
  }

  public function register($username, $email, $phone, $password, $passwordCheck) {
    if ($password != $passwordCheck) {
      return ["success" => false, "message"=> "Пароли не совпадают " . $password . " " . $passwordCheck];
    }

    $formatedPhone = $this->formatPhoneNumber($phone);

    $users = $this->storage->read("users.json");

    // Проверка, существует ли уже пользователь
    foreach ($users as $user) {
      if ($user["username"] === $username) {
        return ["success" => false, "message" => "Пользователь с таким именем уже существует"];
      }
      if ($user["email"] === $email) {
        return ["success" => false, "message"=> "Данный email уже используется"];
      }
      if ($user["phone"] === $formatedPhone) {
        return ["success" => false, "message"=> "Данный телефон уже используется"];
      }
    }

    // Создаём пользователя
    $userID = $this->storage->generateID();
    $newUser = [
      "id" => $userID,
      "username" => $username,
      "email" => $email,
      "phone" => $formatedPhone,
      "password" => password_hash($password, PASSWORD_BCRYPT),
      "creation_date" => date("d-m-Y H:i:s"),
    ];
    $users[] = $newUser;

    if ($this->storage->write("users.json", $users)) {
      return ["success" => true,"message"=> "Пользователь успешно зарегестрирован"];
    }
    
    return ["success" => false,"message"=> "Не удалось зарегестрировать пользователя"];
  }

  public function login($login, $password) {
    $users = $this->storage->read("users.json");

    $formatedPhone = $this->formatPhoneNumber($login);

    foreach ($users as $user) {
      if (($user["email"] == $login || $user["phone"] == $formatedPhone) && password_verify($password, $user["password"])) {
        $payload = [
          "user_id" => $user["id"],
          "username" => $user["username"],
          "email" => $user["email"],
          "phone" => $user["phone"],
          "iat" => time(),
          "exp" => time() + Config::JWT_EXPIRY,
        ];

        $token = JWT::encode($payload);
        $this->storeActiveToken($user["id"], $token);

        return [
          "success" => true,
          "token" => $token,
          "user" =>[
            "id" => $user["id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "phone" => $user["phone"],
          ]
        ];
      }
    }

    return ["success"=> false,"message"=> "Пользователь с такими данными не найден"];
  }

  public function logout($token) {
    if (!isset($token)) {
      return ["success" => false,"message" => "Токен не найден"];
    }

    $blacklist = $this->storage->read("token_blacklist.json");

    // Очистить просроченные токены в черном списке
    $blacklist = array_filter($blacklist, function($item) {
        $blacklistedTime = strtotime($item['blacklisted_at']);
        return (time() - $blacklistedTime) < Config::JWT_EXPIRY;
    });

    $blacklist[] = [
      "token" => $token,
      "blacklisted_at" => date("d-m-Y H:i:s"),
    ];
    
    $this->storage->write("token_blacklist.json", $blacklist);

    $this->removeActiveToken($token); 

    SessionHelper::logout();

    return ["success" => true, "message" => "Выход выполнен успешно"];
  }

  public function verifyToken() {
    $token = SessionHelper::getToken();

    if (empty($token)) {
      http_response_code(401);
      throw new Exception("Токен не предоставлен");
    }

    if ($this->isTokenBlacklisted($token)) {
      http_response_code(401);
      throw new Exception("Токен был отозван");
    }

    try {
      $payload = JWT::decode($token);
      return $payload;
    } catch (Exception $e) {
      http_response_code(401);
      throw new Exception($e->getMessage());
    }
  } 

  public function check() {
    try {
      return $this->verifyToken();
    } catch (Exception $e) {
      return null;
    }
  } 

  private function storeActiveToken($userID, $token) {
    $sessions = $this->storage->read("active_sessions.json");

    // Очистить истёкшие сессии
    $currentTime = time();
    $sessions = array_filter($sessions, function($session) use ($currentTime) {
        try {
            $payload = JWT::decode($session['token']);
            // Keep only if not expired
            return isset($payload['exp']) && $payload['exp'] > $currentTime;
        } catch (Exception $e) {
            // Remove invalid tokens
            return false;
        }
    });

    $sessions[] = [
      "user_id" => $userID,
      "token" => $token,
      "created_at" => date("d-m-Y H:i:s"),
      "expires_at" => date("d-m-Y H:i:s", time() + Config::JWT_EXPIRY),
    ];

    $this->storage->write("active_sessions.json", $sessions);
  }

  private function removeActiveToken($token) {
    $sessions = $this->storage->read('active_sessions.json');
    $sessions = array_filter($sessions, function ($session) use ($token) {
      return $session['token'] !== $token;
    });
    $this->storage->write('active_sessions.json', array_values($sessions));
  }

  private function isTokenBlacklisted($token) {
    $blacklist = $this->storage->read("token_blacklist.json");
    foreach ($blacklist as $item) {
      if ($item["token"] == $token) {
        return true;
      }
      return false;
    }
  }

  public function change($username, $email, $phone, $password, $newPassword) {
    $users = $this->storage->read("users.json");
    $userID = SessionHelper::getUser()["id"] ?? null;
    $userIndex = null;
    
    foreach ($users as $index => $user) {
      if ($user["id"] == $userID) {
        $userIndex = $index;
        break;
      }
    }

    if ($userID === null || $userIndex === null) {
      return ["success" => false, "message" => "Неверные данные"];
    }

    if (!password_verify($password, $users[$userIndex]["password"])) {
      return ["success" => false, "message" => "Неверный пароль"];
    }
    
    if ($username !== $users[$userIndex]["username"]) {
      foreach ($users as $user) {
        if ($user["username"] === $username && $user["id"] !== $userID) {
          return ["success" => false, "message" => "Пользователь с таким именем уже существует"];
        }
      }
    }

    if ($email !== $users[$userIndex]["email"]) {
      foreach ($users as $user) {
        if ($user["email"] === $email && $user["id"] !== $userID) {
          return ["success" => false, "message" => "Email уже занят"];
        }
      }
    }

    $formatedPhone = $this->formatPhoneNumber($phone);

    if ($formatedPhone !== $users[$userIndex]["phone"] && isset($formatedPhone)) {
      foreach ($users as $user) {
        if ($user["phone"] === $formatedPhone && $user["id"] !== $userID) {
          return ["success" => false, "message" => "Номер телефона уже занят"];
        }
      }
    }

    if ($newPassword) {
      if (!$password) {
        return ["success" => false, "message" => "Введите пароль"];
      }
      if (strlen($newPassword) < 8) {
        return ["success" => false, "message" => "Новый пароль должен содержать не менее 8 символов"];
      }
      $users[$userIndex]["password"] = password_hash($newPassword, PASSWORD_BCRYPT);
    }

    // Обновить данные
    if (!empty($username)) {
      $users[$userIndex]["username"] = $username;
    }
    if (!empty($email)) {
      $users[$userIndex]["email"] = $email;
    }
    if (!empty($phone)) {
      $users[$userIndex]["phone"] = $formatedPhone;
    }
    $users[$userIndex]['updated_at'] = date('d-m-Y H:i:s');
    
    $payload = [
          "user_id" => $users[$userIndex]["id"],
          "username" => $users[$userIndex]["username"],
          "email" => $users[$userIndex]["email"],
          "phone" => $users[$userIndex]["phone"],
          "iat" => time(),
          "exp" => time() + Config::JWT_EXPIRY,
        ];

    $token = JWT::encode($payload);
    $this->storeActiveToken($users[$userIndex]["id"], $token);

    if ($this->storage->write("users.json", $users)) {
      return [
        "success" => true,
        "message" => "Данные пользователя обновлены",
        "token" => $token,
        "user" => [
          "id" => $users[$userIndex]["id"],
          "username"=> $users[$userIndex]["username"],
          "email"=> $users[$userIndex]["email"],
          "phone"=> $users[$userIndex]["phone"],
        ]
      ];
    }
    
    return ["success" => false,"message"=> "Не удалось обновить данные пользователя"];
  }

  public function getUser($userID) {
    $users = $this->storage->read("users.json");

    $result = null;
    
    foreach ($users as $user) {
      if ($user["id"] == $userID) {
        $result = $user;
        break;
      }
    }

    unset($result["password"]);
    
    return $result;
  }

  public function formatPhoneNumber($phoneNumber) {
    $phoneNumber = trim($phoneNumber);
    return preg_replace(
      "~.*?(\d{1,3})[^\d]*(\d{3})[^\d]*(\d{3})[^\d]*(\d{2})[^\d]*(\d{2})(?:[ \D#\-]*)?.*~",
      "+$1 ($2) $3 $4-$5", 
      $phoneNumber);
  }

}