<?php
error_reporting(0);
ini_set('display_errors', 0);

ob_start();

require_once dirname(dirname(__DIR__)) . '/controllers/PetApiController.php';
require_once dirname(dirname(__DIR__)) . '/models/Animal.php';
require_once dirname(dirname(__DIR__)) . '/utils/ajax_auth.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$controller = new PetApiController();

$action = $_GET['action'] ?? '';

switch ($action) {    case 'list':
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
          default:
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action specified'
        ]);
        exit;
}
