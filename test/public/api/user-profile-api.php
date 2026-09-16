<?php
/**
 * API Endpoint for User Profile Operations
 * 
 * This endpoint delegates all user profile operations to UserApiController.
 */

// Suppress any PHP errors and warnings
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to capture any unexpected output
ob_start();

// Required files
require_once dirname(dirname(__DIR__)) . '/controllers/UserApiController.php';

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Create controller instance
$controller = new UserApiController();

// Handle request based on method and action
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_profile':
        $userId = $_GET['id'] ?? null;
        $controller->getUserProfile($userId);
        break;
        
    case 'update_profile':
        $controller->updateProfile();
        break;
        
    case 'get_stats':
        $userId = $_GET['id'] ?? null;
        $controller->getUserStats($userId);
        break;
        
    default:
        // Clear output buffer
        ob_clean();
        
        // Send error response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action specified'
        ]);
        exit;
}
