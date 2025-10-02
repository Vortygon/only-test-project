<?php

// Определяем пути
function registerRoutes($router) {
  require_once __DIR__ . "/auth/session_helper.php";
  $auth = new Auth();

  // Создание путей
  $createRoute = function($method, $path, $protected, $callback) use ($router) {
    if (strpos($path, "/api/") === 0) {
      $router->addApiRoute($method, $path, $protected, $callback);
    }
    else {
      $router->addRoute($method, $path, $protected, $callback);
    }
  };

  $get = function ($path, $protected, $callback) use ($createRoute)  {
    $createRoute("GET", $path, $protected, $callback);
  };

  $post = function ($path, $protected, $callback) use ($createRoute) {
    $createRoute("POST", $path, $protected, $callback);
  };

  $put = function ($path, $protected, $callback) use ($createRoute) {
    $createRoute("PUT", $path, $protected, $callback);
  };

  $patch = function ($path, $protected, $callback) use ($createRoute) {
    $createRoute("POST", $path, $protected, $callback);
  };

  $delete = function ($path, $protected, $callback) use ($createRoute) {
    $createRoute("DELETE", $path, $protected, $callback);
  };

  /**
   *
   * Пути
   *
   */

  // Главная страница
  $get("/", false, function () use ($router) {
    if (SessionHelper::getUser()) {
        header("Location: /profile");
        exit;
    }

    $router->renderPage("index");
  });

  // Страница ошибки 404
  $get("/404", false, function () use ($router) {
    $router->renderPage("/404");
  });

  // Страница данных пользователя
  $get("/profile", true, function () use ($router) {
    $user = SessionHelper::getUser();
    $userCheck = json_decode($router->requestInternalApi("GET", "/api/user"), true);

    // Проверяем актуальность данных
    foreach ($userCheck as $key => $value) {
      if ($user[$key] !== $value) {
        SessionHelper::setUser($userCheck, SessionHelper::getToken());
        break;
      }
    }

    header("Content-type: text/html; charset=UTF-8");
    $router->renderPage("/profile");
  });

  // Обработка формы регистрации
  $post("/auth/register", false, function () use ($router) {
    if (SessionHelper::getUser()) {
        header("Location: /profile");
        exit;
    }

    $res = json_decode($router->requestInternalApi("POST", "/api/auth/register"), true);
    $_SESSION["message"] = $res["message"];
    

    $params = ["username", "email", "phone"]; 
    $redirectParams = [];
    foreach ($params as $key) {
      if (isset($_POST[$key])) {
        $redirectParams[] = $key . "=" . $_POST[$key];
      }
    }
    if ($res["success"] === true) {
      header("Location: /auth/login?login=" . $_POST["email"]);
      exit;
    }
    header("Location: /auth/register?" . implode("&", $redirectParams));
    exit;
  });

  // Обработка формы авторизации
  $post("/auth/login", false, function () use ($router) {
    if (SessionHelper::getUser()) {
        header("Location: /profile");
        exit;
    }

    $res = json_decode($router->requestInternalApi("POST", "/api/auth/login"), true);
    $_SESSION["message"] = $res["message"];
    $redirectParams = isset($_POST["login"]) ? $_POST["login"] : null;
    header("Location: /auth/login" . (isset($redirectParams) ? "?login=" . $redirectParams : ""));
    exit;
  });

  $get("/auth/logout", true, function () use ($router) {
    $router->requestInternalApi("POST", "/api/auth/logout");
    header("Location: /");
    exit;
  });

  // Обработка формы изменения данных
  $post("/profile", true, function () use ($router) {
    $res = json_decode($router->requestInternalApi("POST", "/api/auth/change"), true);
    $_SESSION["message"] = $res["message"];
    // exit;
    header("Location: /profile");
    exit;
  });

  /** ------------------------------------------------------------- */
  
  /**
   *
   * Точки API
   * 
   */

  /**
   * Списки путей
   */

  // Список путей к страницам
  $get("/api/routes", false, function () use ($router) {
    return $router->getRoutes();
  });

  // Список точек API 
  $get("/api/api_routes", false, function () use ($router) {
    return $router->getApiRoutes();
  });
  
  // Данные пользователя
  $get("/api/user", false, function () use ($auth) {
    $userID = SessionHelper::getUser()["id"];
    return $auth->getUser($userID);
  });

  /**
   * Авторизация
   */

  // Регистрация пользователя
  $post("/api/auth/register", false, function () use ($auth) {
    if (SessionHelper::getUser()) {
        header("Location: /profile");
        exit;
    }
    
    $message = null;

    $username = $_POST["username"] ?? "";
    $email = $_POST["email"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $password = $_POST["password"] ?? "";
    $passwordCheck = $_POST["passwordCheck"] ?? "";
    
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($passwordCheck)) {
      $message = "Необходимо заполнить все поля";
      SessionHelper::setFlash("fail", $message);
      return ["success" => false, "message" => $message]; 
    }
    if (strlen($password) < 8) {
      $message = "Пароль должен быть длинной не менее 8 символов";
      SessionHelper::setFlash("fail", $message);
      return ["success" => false, "message" => $message]; 
    }
    if ($password !== $passwordCheck) {
      $message = "Пароли не совпадают";
      SessionHelper::setFlash("fail", $message);
      return ["success" => false, "message" => $message]; 
    } 

    // Проверка капчи
    require_once __DIR__ . "/auth/captcha.php";

    if (!checkCaptchaComletion()) {
      $message = "Капча не пройдена";
      SessionHelper::setFlash("fail", $message);
      return ["success" => false, "message" => $message]; 
    };

    $result = $auth->register(
      $username,
      $email,
      $phone, 
      $password,
      $passwordCheck
    );

    return $result;
  });

  // Авторизация пользователя
  $post("/api/auth/login", false, function () use ($auth) {
    if (SessionHelper::getUser()) {
        header("Location: /profile");
        exit;
    }
    
    $login = $_POST["login"] ?? "";
    $password = $_POST["password"] ?? "";
    
    if (empty($login) || empty($password)) {
      $message = "Необходимо заполнить все поля";
      return ["success" => false, "message" => $message]; 
    }

    // Проверка капчи
    require_once __DIR__ . "/auth/captcha.php";

    if (!checkCaptchaComletion()) {
      $message = "Капча не пройдена";
      return ["success" => false, "message" => $message]; 
    };

    $result = $auth->login($login, $password);

    if ($result['success'] === true) {
      SessionHelper::setUser($result["user"], $result["token"]);
    }

    return $result;
  });

  // Деавторизация пользователя
  $post("/api/auth/logout", true, function () use ($auth) {
    $token = SessionHelper::getToken();

    if ($token) {
      return $auth->logout($token);
    } 

    header("Location: /");
    return ["success" => false,"message" => "Не удалось выполнить выход"];
  });

  // Изменение данных пользователя
  $post("/api/auth/change", true, function () use ($auth) {
    $user = SessionHelper::getUser();
    
    $username = $_POST["username"] ?? "";
    $email = $_POST["email"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $newPassword = $_POST["newPassword"] ?? "";
    $password = $_POST["password"] ?? "";
    
    if (empty($password)) {
      $message = "Введите пароль";
      return ["success" => false, "message" => $message];
    }

    if (empty($username) && empty($email) && empty($phone) && empty($newPassword)) {
      $message = "Укажите данные для изменения";
      return ["success" => false, "message" => $message]; 
    }

    $result = $auth->change($username, $email, $phone, $password, $newPassword);
    if ($result["success"] === true) {
      SessionHelper::setUser($result["user"], $result["token"]);
    }

    return $result;
  });

  $get("/api/user", true, function () use ($auth) {
    require_once __DIR__ . "/auth/session_helper.php";

    $user = SessionHelper::getUser() ?? null;
    if ($user) {
      return ["success" => true, "user" => $user];
    }
    return ["success" => false, "message" => "Не удалось получить данные о пользователе"];

  });


  // После того, как определили пути, сканируем директорию /pages/ и добавляем страницы
  $router->scanPagesDir();

}