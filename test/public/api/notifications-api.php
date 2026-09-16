<?php

error_reporting(0);
ini_set('display_errors', 0);

ob_start();

require_once dirname(dirname(__DIR__)) . '/controllers/NotificationApiController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$controller = new NotificationApiController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_notifications':
        $controller->getNotifications();
        break;
        
    case 'mark_read':
        $controller->markAsRead();
        break;
        
    case 'mark_all_read':
        $controller->markAllAsRead();
        break;
        
    case 'get_unread_count':
        $controller->getUnreadCount();
        break;
        
    default:
        ob_clean();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action specified'
        ]);
        exit;
}
