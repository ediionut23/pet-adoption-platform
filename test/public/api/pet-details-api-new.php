<?php

function apiErrorHandler($severity, $message, $file, $line) {
    error_log("PHP Error ($severity): $message in $file on line $line");
    return true;
}

set_error_handler('apiErrorHandler');

error_reporting(0);
ini_set('display_errors', 0);

ob_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../controllers/PetApiController.php';

$controller = new PetApiController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'recent') {
        $controller->getRecentPets();
    } else {
        $controller->getPetDetails();
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
