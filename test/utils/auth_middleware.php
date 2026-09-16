<?php
require_once __DIR__ . '/JWTManager.php';

function checkAuth() {
    if (!isset($_COOKIE['jwt_token'])) {
        header('Location: login.php');
        exit();
    }

    $token = $_COOKIE['jwt_token'];
    $decoded = JWTManager::validateToken($token);

    if (!$decoded) {

        setcookie('jwt_token', '', time() - 3600, '/');
        header('Location: login.php');
        exit();
    }

    return $decoded;
} 