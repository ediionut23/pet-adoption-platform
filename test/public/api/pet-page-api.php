<?php
/**
 * Pet Page API Endpoint
 * Acest fișier servește ca endpoint pentru API-ul care furnizează date pentru pagina de detalii animal
 */

// Define a custom error handler for debugging
function apiErrorHandler($severity, $message, $file, $line) {
    // Log the error to a file instead of displaying it
    error_log("PHP Error ($severity): $message in $file on line $line");
    // Don't display the error
    return true;
}

// Set custom error handler
set_error_handler('apiErrorHandler');

// Suppress any PHP errors and warnings
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to capture any unexpected output
ob_start();

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Load the controller
require_once '../../controllers/PetApiController.php';

// Create controller and process request
$controller = new PetApiController();
// Handle the request based on HTTP method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->getPetPageData();
} else {
    // Return error for unsupported methods
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
