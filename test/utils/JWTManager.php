<?php
require_once __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTManager {
    private static $key = "your-secret-key-here";
    private static $algorithm = 'HS256';

    public static function generateToken($userData) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;

        $payload = array(
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "user" => $userData
        );

        return JWT::encode($payload, self::$key, self::$algorithm);
    }

    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$key, self::$algorithm));
            return $decoded->user;
        } catch (Exception $e) {
            return false;
        }
    }
} 