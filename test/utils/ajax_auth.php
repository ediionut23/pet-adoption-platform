<?php
require_once __DIR__ . '/JWTManager.php';

function validateAjaxRequest() {
    $headers = getallheaders();
    $token = isset($_COOKIE['jwt_token']) ? $_COOKIE['jwt_token'] : null;
    
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - No token provided']);
        exit();
    }

    $decoded = JWTManager::validateToken($token);
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - Invalid token']);
        exit();
    }

    return $decoded['user'];
}

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
} 