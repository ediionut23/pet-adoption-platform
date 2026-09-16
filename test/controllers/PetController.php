<?php
ob_start();

require_once '../models/Pet.php';
require_once '../config/database.php';
require_once '../utils/auth_middleware.php';

function sendJsonResponse($success, $message = null, $data = null) {
    ob_clean();
    header('Content-Type: application/json');
    $response = ['success' => $success];
    
    if ($message !== null) {
        $response['message'] = $message;
    }
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    ob_end_flush();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        sendJsonResponse(false, 'Not authenticated');
    }
    
    $controller = new PetController();
    
    try {
        switch ($_POST['action']) {
            case 'delete':
                $controller->deletePet();
                break;
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        error_log("Error in PetController: " . $e->getMessage());
        sendJsonResponse(false, $e->getMessage());
    }
}

class PetController {
    private $petModel;
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
        $this->petModel = new Pet($this->db);
    }
      public function __destruct() {
        if ($this->db) {
            oci_close($this->db);
        }
    }
    
    public function showPetDetails($id) {
        try {
            $pet_id = (int)$id;
            if ($pet_id <= 0) {
                return ['error' => "Invalid pet ID"];
            }
            
            $pet = $this->petModel->getPetById($pet_id);
            if (!$pet) {
                return ['error' => "Pet not found"];
            }
            
            $media = $this->petModel->getPetMedia($pet_id);
            
            return [
                'pet' => $pet,
                'media' => $media
            ];
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllPetsWithCoordinates() {
        try {
            return $this->petModel->getAllPetsWithCoordinates();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }    public function createPet($data, $files = null) {
        try {
            $requiredFields = [
                'name', 'species', 'breed', 'age', 'gender', 'health_status',
                'personality_description', 'activity_description', 'diet_description',
                'household_activity', 'household_environment', 'other_pets',
                'color', 'marime', 'time_at_current_home', 'reason_for_rehoming',
                'current_owner_description', 'adoption_address', 'latitude', 'longitude'
            ];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || (is_string($data[$field]) && empty(trim($data[$field]))) || 
                    (in_array($field, ['latitude', 'longitude']) && !is_numeric($data[$field]))) {
                    return ['error' => "Field {$field} is required and must be valid"];
                }
            }

            $data['spayed_neutered'] = isset($data['spayed_neutered']) ? 1 : 0;
            $data['flea_treatment'] = isset($data['flea_treatment']) ? 1 : 0;

            $data['latitude'] = floatval($data['latitude']);
            $data['longitude'] = floatval($data['longitude']);

            $pet_id = $this->petModel->createPet($data);
            if (!$pet_id) {
                return ['error' => "Failed to create pet"];
            }

            if ($files && isset($files['pet_images']) && !empty($files['pet_images']['name'][0])) {
                $uploadResult = $this->petModel->uploadPetImages($pet_id, $files['pet_images']);
                if (!$uploadResult['success']) {
                    error_log("Image upload errors for pet {$pet_id}: " . implode(", ", $uploadResult['errors']));
                }
            }

            return [
                'success' => true,
                'pet_id' => $pet_id
            ];

        } catch (Exception $e) {
            error_log("Exception in createPet: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function getUserPets($userId) {
        try {
            return $this->petModel->getPetsByUserId($userId);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deletePet() {
        try {
            if (!isset($_POST['pet_id'])) {
                throw new Exception("Pet ID is required");
            }

            $pet_id = (int)$_POST['pet_id'];
            if ($pet_id <= 0) {
                throw new Exception("Invalid pet ID");
            }

            $user_id = $_SESSION['user_id'];
            if (!$user_id) {
                throw new Exception("User not authenticated");
            }            $pet = $this->petModel->getPetById($pet_id);
            if (!$pet) {
                throw new Exception("Pet not found");
            }

            if ($pet['OWNER_ID'] != $user_id) {
                throw new Exception("You don't have permission to delete this pet");
            }

            $result = $this->petModel->deletePet($pet_id);
            
            if ($result) {
                sendJsonResponse(true);
            } else {
                sendJsonResponse(false, 'Failed to delete pet');
            }

        } catch (Exception $e) {
            error_log("Error deleting pet: " . $e->getMessage());
            sendJsonResponse(false, $e->getMessage());
        }
    }

    public function getDefaultPetImage($petId, $species) {
        try {
            $media = $this->petModel->getPetMedia($petId);
            if (!empty($media) && isset($media[0]['URL']) && !empty($media[0]['URL'])) {
                return '../' . $media[0]['URL'];
            }

            $defaultImages = [
                'Dog' => '../stiluri/imagini/dog.png',
                'Cat' => '../stiluri/imagini/cat.png',
                'Bird' => '../stiluri/imagini/bird.png',
                'Fish' => '../stiluri/imagini/fish.png',
                'Reptile' => '../stiluri/imagini/reptilian.png'
            ];

            return isset($defaultImages[$species]) ? $defaultImages[$species] : '';
        } catch (Exception $e) {
            return '';
        }
    }
}