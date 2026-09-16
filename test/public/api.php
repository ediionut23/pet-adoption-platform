<?php
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function sendErrorResponse($message) {
    ob_clean();
    
    error_log("API Error: " . $message . " | Request: " . json_encode($_REQUEST));
    
    echo json_encode([
        'success' => false,
        'message' => $message,
        'request_data' => [
            'type' => $_GET['type'] ?? null,
            'action' => $_GET['action'] ?? null,
            'method' => $_SERVER['REQUEST_METHOD'],
            'query_string' => $_SERVER['QUERY_STRING'] ?? null
        ]
    ]);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');

function fatal_handler() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal Server Error',
            'debug' => $error['message']
        ]);
        exit();
    }
}
register_shutdown_function('fatal_handler');

try {
    session_start();

    $type = $_GET['type'] ?? '';
    $action = $_GET['action'] ?? '';

error_log("API.php received request: " . json_encode($_GET));

switch ($type) {
    case 'pets':
        require_once dirname(__DIR__) . '/controllers/PetApiController.php';
        $controller = new PetApiController();

        switch ($action) {
            case 'list':
                $controller->listAnimals();
                break;
                
            case 'get_details':
                $petId = $_GET['id'] ?? null;
                $controller->getPetDetails($petId);
                break;
                
            case 'get_locations':
                $controller->getPetLocations();
                break;
                  case 'get_pet_page_data':
                $controller->getPetPageData();
                break;
                  case 'get_user_pets':
                $userId = $_GET['user_id'] ?? null;
                $controller->getUserPets($userId);
                break;
                  case 'delete_pet':
                $petId = $_GET['id'] ?? null;
                $controller->deletePet($petId);
                break;
                
            case 'create_pet':
                $controller->createPet();
                break;
                
            default:
                sendErrorResponse('Invalid action specified for pets API');
        }
        break;
        
    case 'user':
        require_once dirname(__DIR__) . '/controllers/UserController.php';
        $controller = new UserController();
        
        switch ($action) {
            case 'register':
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $result = $controller->register($data);
                
                if ($result['success']) {
                    http_response_code(200);
                } else {
                    http_response_code(400);
                }
                
                echo json_encode($result);
                exit;
                break;

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
                sendErrorResponse('Invalid action specified for user API');
        }
        break;
          case 'notifications':
        require_once dirname(__DIR__) . '/controllers/NotificationController_Unified.php';
        $controller = new NotificationController();
        
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
                sendErrorResponse('Invalid action specified for notifications API');
        }
        break;          case 'adoption_form':
    case 'adoption': 
        require_once dirname(__DIR__) . '/controllers/AdoptionFormController.php';
        require_once dirname(__DIR__) . '/utils/auth_middleware.php';
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'errors' => ['Please log in to submit an adoption form']
            ]);
            exit;
        }
        
        $controller = new AdoptionFormController();
        
        switch ($action) {
            case 'submit':
                $petId = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;
                
                if (!$petId) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'errors' => ['Invalid pet ID']
                    ]);
                    exit;
                }
                
                $errors = $controller->validateForm($_POST);
                
                if (empty($errors)) {
                    $result = $controller->processForm($_POST, $petId, $_SESSION['user_id']);
                    
                    if (isset($result['error'])) {
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'errors' => [$result['error']]
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Adoption form submitted successfully'
                        ]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'errors' => $errors
                    ]);
                }
                exit;
                break;
                
            case 'check_existing':
                $petId = $_GET['pet_id'] ?? null;
                $controller->checkExistingSubmission($petId);
                break;
                
            default:
                sendErrorResponse('Invalid action specified for adoption form API');
        }
        break;
        
    default:
        sendErrorResponse('Invalid API type: ' . htmlspecialchars($type) . '. Expected one of: pets, user, notifications, adoption_form');
}

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Internal Server Error',
        'debug' => $e->getMessage() 
    ]);
}
