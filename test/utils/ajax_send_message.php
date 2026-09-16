<?php
require_once 'auth_middleware.php';
$user = checkAuth();

session_start();
require_once '../controllers/MessageController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$controller = new MessageController();
$result = $controller->sendMessage();

echo json_encode($result); 