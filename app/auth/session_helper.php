<?php

class SessionHelper {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $params = session_get_cookie_params();
            setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*25*30, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
        }
    }
    
    public static function setUser($user, $token) {
        self::start();
        $_SESSION['user'] = $user;
        $_SESSION['token'] = $token;
    }
    
    public static function getUser() {
        self::start();
        return $_SESSION['user'] ?? null;
    }
    
    public static function getToken() {
        self::start();
        return $_SESSION['token'] ?? null;
    }
    
    public static function logout() {
        self::start();
        session_unset();
        session_destroy();
    }
    
    public static function setFlash($type, $message) {
        self::start();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
    
    public static function getFlash() {
        self::start();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}