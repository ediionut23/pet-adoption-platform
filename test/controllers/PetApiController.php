<?php

error_reporting(0);
ini_set('display_errors', 0);

ob_start();

require_once dirname(dirname(__FILE__)) . '/config/database.php';
require_once dirname(dirname(__FILE__)) . '/models/Pet.php';

class PetApiController {
    private $conn;
    private $petModel;
    

    public function __construct() {
        try {
            $this->conn = getConnection();
            $this->petModel = new Pet($this->conn);
        } catch (Exception $e) {
            error_log("Database connection error in PetApiController: " . $e->getMessage());
        }
    }
    

    public function __destruct() {
        if ($this->conn) {
            oci_close($this->conn);
        }
    }
    

    private function sendJsonResponse($success, $message = null, $data = []) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        $response = ['success' => $success];
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        if (!empty($data)) {
            $data = $this->sanitizeForJson($data);
            $response = array_merge($response, $data);
        }
        
        try {
            $jsonOutput = json_encode($response);
            
            if ($jsonOutput === false) {
                $jsonError = json_last_error_msg();
                error_log("JSON encoding error: " . $jsonError);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Could not encode response data',
                    'error' => $jsonError
                ]);
            } else {
                echo $jsonOutput;
            }
        } catch (Exception $e) {
            error_log("Exception during JSON encoding: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while processing the response'
            ]);
        }
        
        exit;
    }
    

    private function sanitizeForJson($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeForJson($value);
            }
            return $data;
        } elseif (is_object($data)) {
            $data = (array)$data;
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeForJson($value);
            }
            return $data;
        } elseif (is_string($data)) {
            if (!mb_check_encoding($data, 'UTF-8')) {
                return mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
            }
            return $data;
        } elseif (is_resource($data)) {
            return "Resource";
        } else {
            return $data;
        }
    }
    

    public function getPetPageData() {
        $petDetails = null;
        $media = [];
        $petLocations = [];
        $errorMessages = [];
        
        try {
            if (!$this->conn) {
                $this->sendJsonResponse(false, 'Database connection failed');
            }
            
            if (!isset($_GET['id'])) {
                $this->sendJsonResponse(false, 'Pet ID is required');
            }
            
            $petId = (int)$_GET['id'];
            if ($petId <= 0) {
                $this->sendJsonResponse(false, 'Invalid pet ID');
            }
            
            try {
                $petDetails = $this->petModel->getPetById($petId);
                if (!$petDetails) {
                    $this->sendJsonResponse(false, 'Pet not found');
                }
            } catch (Exception $e) {
                error_log("Error getting pet details: " . $e->getMessage());
                $this->sendJsonResponse(false, 'Error retrieving pet details');
            }
            
            try {
                $media = $this->petModel->getPetMedia($petId);
                if (!is_array($media)) {
                    $media = [];
                }
            } catch (Exception $e) {
                error_log("Error getting pet media: " . $e->getMessage());
                $errorMessages[] = 'Could not retrieve pet images';
            }
            
            try {
                $petLocations = $this->petModel->getAllPetsWithCoordinates();
                if (!is_array($petLocations)) {
                    $petLocations = [];
                }
            } catch (Exception $e) {
                error_log("Error getting pet locations: " . $e->getMessage());
                $errorMessages[] = 'Could not retrieve pet locations for map';
            }
            
            $this->sendJsonResponse(true, null, [
                'pet' => $petDetails,
                'media' => $media,
                'allPetsWithCoordinates' => $petLocations,
                'warnings' => $errorMessages
            ]);
            
        } catch (Exception $e) {
            error_log("Pet page API error: " . $e->getMessage());
            $this->sendJsonResponse(false, 'Error processing request: ' . $e->getMessage());
        }
    }

    public function getPetLocations() {
        try {
            if (!$this->conn) {
                $this->sendJsonResponse(false, 'Database connection failed');
            }
            
            $pets = $this->petModel->getAllPetsWithCoordinates();
            if (!is_array($pets)) {
                $pets = [];
            }
            
            $this->sendJsonResponse(true, null, ['pets' => $pets]);
        } catch (Exception $e) {
            error_log("Error getting pet locations: " . $e->getMessage());
            $this->sendJsonResponse(false, 'Error retrieving pet locations');
        }
    }
    
    /**
     * Get details for a specific pet by ID
     */
    public function getPetDetails() {
        try {
            if (!$this->conn) {
                $this->sendJsonResponse(false, 'Database connection failed');
            }
            
            if (!isset($_GET['id'])) {
                $this->sendJsonResponse(false, 'Pet ID is required');
            }
            
            $petId = (int)$_GET['id'];
            if ($petId <= 0) {
                $this->sendJsonResponse(false, 'Invalid pet ID');
            }
            
            $petDetails = $this->petModel->getPetById($petId);
            if (!$petDetails) {
                $this->sendJsonResponse(false, 'Pet not found');
            }
            
            $media = $this->petModel->getPetMedia($petId);
            if (!is_array($media)) {
                $media = [];
            }
            
            $restrictions = [];
            $feedingSchedule = [];
            $medicalHistory = [];
            
            if (isset($_GET['include_restrictions']) && $_GET['include_restrictions'] == 'true') {
                try {
                    $restrictions = $this->petModel->getPetRestrictions($petId);
                } catch (Exception $e) {
                    error_log("Error getting pet restrictions: " . $e->getMessage());
                }
            }
            
            if (isset($_GET['include_feeding']) && $_GET['include_feeding'] == 'true') {
                try {
                    $feedingSchedule = $this->petModel->getPetFeedingSchedule($petId);
                } catch (Exception $e) {
                    error_log("Error getting feeding schedule: " . $e->getMessage());
                }
            }
            
            if (isset($_GET['include_medical']) && $_GET['include_medical'] == 'true') {
                try {
                    $medicalHistory = $this->petModel->getPetMedicalHistory($petId);
                } catch (Exception $e) {
                    error_log("Error getting medical history: " . $e->getMessage());
                }
            }
            
            $this->sendJsonResponse(true, null, [
                'pet' => $petDetails,
                'media' => $media,
                'restrictions' => $restrictions,
                'feedingSchedule' => $feedingSchedule,
                'medicalHistory' => $medicalHistory
            ]);
        } catch (Exception $e) {
            error_log("Error getting pet details: " . $e->getMessage());
            $this->sendJsonResponse(false, 'Error retrieving pet details');
        }
    }
    

    public function getRecentPets() {
        try {
            if (!$this->conn) {
                $this->sendJsonResponse(false, 'Database connection failed');
            }
            
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
            if ($limit <= 0 || $limit > 20) {
                $limit = 6; 
            }
            
            $recentPets = [];
            
            $query = "SELECT p.id, p.name, p.species, p.breed, p.gender, p.age, p.available_for_adoption 
                     FROM pets p 
                     WHERE p.available_for_adoption = 1 
                     ORDER BY p.id DESC 
                     FETCH FIRST :limit ROWS ONLY";
                     
            $stmt = oci_parse($this->conn, $query);
            oci_bind_by_name($stmt, ":limit", $limit);
            oci_execute($stmt);
            
            while ($row = oci_fetch_assoc($stmt)) {
                $imageUrl = '';
                try {
                    $media = $this->petModel->getPetMedia($row['ID']);
                    if (is_array($media) && !empty($media)) {
                        $imageUrl = '../' . $media[0]['URL'];
                    } else {
                        $imageUrl = '../stiluri/imagini/' . strtolower($row['SPECIES']) . '.png';
                    }
                } catch (Exception $e) {
                    $imageUrl = '../stiluri/imagini/' . strtolower($row['SPECIES']) . '.png';
                }
                
                $recentPets[] = [
                    'id' => $row['ID'],
                    'name' => $row['NAME'],
                    'species' => $row['SPECIES'],
                    'breed' => $row['BREED'],
                    'gender' => $row['GENDER'],
                    'age' => $row['AGE'],
                    'image' => $imageUrl
                ];
            }
            
            $this->sendJsonResponse(true, null, ['pets' => $recentPets]);
            
        } catch (Exception $e) {
            error_log("Error getting recent pets: " . $e->getMessage());
            $this->sendJsonResponse(false, 'Error retrieving recent pets');
        }
    }
    
       public function listAnimals() {
        try {
            error_log("listAnimals method called with parameters: " . json_encode($_GET));
            
            if (!$this->conn) {
                error_log("listAnimals: Database connection is null");
                return $this->sendJsonResponse(false, 'Database connection error');
            }
            $type = $_GET['animal_type'] ?? null; 
            $gender = $_GET['sex'] ?? null;
            $age = $_GET['age'] ?? null;
            $size = $_GET['weight'] ?? null;
            try {
                require_once dirname(dirname(__FILE__)) . '/models/Animal.php';
                require_once dirname(dirname(__FILE__)) . '/config/database.php';
                $animals = Animal::filterAnimals($type, $gender, $age, $size);
            } catch (Exception $e) {
                error_log("Error loading Animal model or filtering animals: " . $e->getMessage());
                return $this->sendJsonResponse(false, 'Error loading animal data: ' . $e->getMessage());
            }
            
            $result = [];
            if (!empty($animals)) {
                foreach ($animals as $animal) {
                    $query = "SELECT url FROM media WHERE pet_id = :pet_id AND type = 'photo' AND ROWNUM = 1";
                    $stmt = oci_parse($this->conn, $query);
                    oci_bind_by_name($stmt, ":pet_id", $animal['id']);
                    oci_execute($stmt);
                    $image = oci_fetch_assoc($stmt);
                    oci_free_statement($stmt);
                    $imageUrl = '../stiluri/imagini/' . strtolower($animal['species']) . '.png'; // Default image
                    if ($image) {
                        if (isset($image['URL'])) {
                            $imageUrl = '../' . $image['URL'];
                        } else if (isset($image['url'])) {
                            $imageUrl = '../' . $image['url'];
                        }
                    }

                    $animalData = [
                        'id' => $animal['id'],
                        'name' => $animal['name'],
                        'species' => $animal['species'],
                        'breed' => $animal['breed'] ?? '',
                        'age' => $animal['age'] ?? '',
                        'gender' => $animal['gender'] ?? '',
                        'available_for_adoption' => $animal['available_for_adoption'] ?? 1,
                        'image' => $imageUrl
                    ];
                    
                    $result[] = $animalData;
                }
            }
            
            return $this->sendJsonResponse(true, null, ['animals' => $result]);
            
        } catch (Exception $e) {
            error_log("Error in listAnimals: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while retrieving animals');
        }
    }
    
    /**
     * Get all pets for a specific user
     */
    public function getUserPets($userId = null) {
        try {
            if (!$this->conn) {
                return $this->sendJsonResponse(false, 'Database connection error');
            }
            
            if ($userId === null) {
                if (isset($_GET['user_id'])) {
                    $userId = (int)$_GET['user_id'];
                } else {
                    session_start();
                    $userId = $_SESSION['user_id'] ?? null;
                }
            }
            
            if (!$userId) {
                return $this->sendJsonResponse(false, 'Invalid or missing user ID');
            }
            
            $pets = $this->petModel->getUserPets($userId);
              if (empty($pets)) {
                return $this->sendJsonResponse(true, 'No pets found', ['pets' => []]);
            }

            foreach ($pets as &$pet) {
                try {
                    $query = "SELECT url FROM media WHERE pet_id = :pet_id AND type = 'photo' AND ROWNUM = 1";
                    $stmt = oci_parse($this->conn, $query);
                    if (!$stmt) {
                        throw new Exception("Failed to prepare media query");
                    }

                    oci_bind_by_name($stmt, ":pet_id", $pet['ID']);
                    $execute = oci_execute($stmt);
                    
                    if (!$execute) {
                        throw new Exception("Failed to execute media query");
                    }

                    $image = oci_fetch_assoc($stmt);
                    oci_free_statement($stmt);
                    
                    $species = strtolower($pet['SPECIES'] ?? 'default');
                    $defaultImage = '../stiluri/imagini/' . $species . '.png';
                      $pet['IMAGE'] = ($image && isset($image['URL'])) 
                        ? '../' . trim($image['URL'])
                        : $defaultImage;
                } catch (Exception $mediaError) {
                    error_log("Error getting media for pet {$pet['ID']}: " . $mediaError->getMessage());
                    $pet['IMAGE'] = '../stiluri/imagini/' . strtolower($pet['SPECIES'] ?? 'default') . '.png';
                }
            }
            
            return $this->sendJsonResponse(true, 'Pets retrieved successfully', ['pets' => $pets]);
            
        } catch (Exception $e) {
            error_log("Error in getUserPets: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while retrieving user pets');
        }
    }
      public function deletePet($petId = null) {
        try {
            if (!$this->conn) {
                return $this->sendJsonResponse(false, 'Database connection error');
            }
            
            if ($petId === null) {
                if (isset($_GET['id'])) {
                    $petId = (int)$_GET['id'];
                }
            }
            
            if (!$petId) {
                return $this->sendJsonResponse(false, 'Invalid or missing pet ID');
            }
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                return $this->sendJsonResponse(false, 'User not authenticated');
            }
            
            try {
                $result = $this->petModel->deletePet($petId);
                if ($result) {
                    return $this->sendJsonResponse(true, 'Pet deleted successfully');
                }
            } catch (Exception $e) {
                return $this->sendJsonResponse(false, $e->getMessage());
            }
            $query = "SELECT owner_id FROM pets WHERE id = :pet_id";
            $stmt = oci_parse($this->conn, $query);
            oci_bind_by_name($stmt, ":pet_id", $petId);
            oci_execute($stmt);
            $pet = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);
            
            if (!$pet || $pet['OWNER_ID'] != $userId) {
                return $this->sendJsonResponse(false, 'You do not have permission to delete this pet');
            }
            
            $mediaQuery = "DELETE FROM media WHERE pet_id = :pet_id";
            $mediaStmt = oci_parse($this->conn, $mediaQuery);
            oci_bind_by_name($mediaStmt, ":pet_id", $petId);
            oci_execute($mediaStmt);
            oci_free_statement($mediaStmt);
            
            $formQuery = "DELETE FROM adoption_forms WHERE pet_id = :pet_id";
            $formStmt = oci_parse($this->conn, $formQuery);
            oci_bind_by_name($formStmt, ":pet_id", $petId);
            oci_execute($formStmt);
            oci_free_statement($formStmt);
            
            $requestQuery = "DELETE FROM adoption_requests WHERE pet_id = :pet_id";
            $requestStmt = oci_parse($this->conn, $requestQuery);
            oci_bind_by_name($requestStmt, ":pet_id", $petId);
            oci_execute($requestStmt);
            oci_free_statement($requestStmt);
            
            $deleteQuery = "DELETE FROM pets WHERE id = :pet_id";
            $deleteStmt = oci_parse($this->conn, $deleteQuery);
            oci_bind_by_name($deleteStmt, ":pet_id", $petId);
            $result = oci_execute($deleteStmt);
            oci_free_statement($deleteStmt);
            
            if ($result) {
                return $this->sendJsonResponse(true, 'Pet deleted successfully');
            } else {
                return $this->sendJsonResponse(false, 'Failed to delete pet');
            }
            
        } catch (Exception $e) {
            error_log("Error in deletePet: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while deleting the pet');
        }
    }
    
    /**
     * Create a new pet
     */    public function createPet() {
        try {
            if (!$this->conn) {
                return $this->sendJsonResponse(false, 'Database connection error');
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->sendJsonResponse(false, 'This endpoint only accepts POST requests');
            }
            
            session_start();
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                return $this->sendJsonResponse(false, 'User not authenticated');
            }
            $petData = $_POST;
            $files = $_FILES;
            
            error_log("Creating pet with data: " . json_encode($petData));
            error_log("Files received: " . json_encode(array_keys($files)));

            if (!empty($files)) {
                foreach ($files as $fieldName => $fileInfo) {
                    $fileCount = is_array($fileInfo['name']) ? count($fileInfo['name']) : 1;
                    error_log("Field {$fieldName}: {$fileCount} file(s) received");
                    
                    if (is_array($fileInfo['name'])) {
                        for ($i = 0; $i < count($fileInfo['name']); $i++) {
                            error_log("File {$i}: Name={$fileInfo['name'][$i]}, " . 
                                "Size={$fileInfo['size'][$i]}, " . 
                                "Type={$fileInfo['type'][$i]}, " . 
                                "Error={$fileInfo['error'][$i]}, " .
                                "Tmp={$fileInfo['tmp_name'][$i]}");
                        }
                    }
                }
            } else {
                error_log("WARNING: No files received in the request!");
            }
            $requiredFields = ['name', 'species', 'breed', 'age', 'gender'];
            $missingFields = [];
            $invalidFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($petData[$field]) || trim($petData[$field]) === '') {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                return $this->sendJsonResponse(false, 'Missing required fields: ' . implode(', ', $missingFields));
            }
            
            if (isset($petData['age']) && !is_numeric($petData['age'])) {
                $invalidFields[] = 'age (must be a number)';
            }
            
            if (isset($petData['spayed_neutered']) && $petData['spayed_neutered'] !== '0' && $petData['spayed_neutered'] !== '1' && 
                $petData['spayed_neutered'] !== 0 && $petData['spayed_neutered'] !== 1) {
                $petData['spayed_neutered'] = 0;
            }
            
            if (!empty($invalidFields)) {
                return $this->sendJsonResponse(false, 'Invalid field values: ' . implode(', ', $invalidFields));
            }
            
            $petData['owner_id'] = $userId;
            
            if (!isset($petData['description']) && isset($petData['personality_description'])) {
                $petData['description'] = $petData['personality_description'];
            }
            
            if (!isset($petData['adoption_address']) || empty($petData['adoption_address'])) {
                $petData['adoption_address'] = 'Romania'; 
            }
            
            $result = $this->petModel->createPet($petData, $files);
            
            if (isset($result['success']) && $result['success']) {
                return $this->sendJsonResponse(true, 'Pet created successfully', [
                    'pet_id' => $result['pet_id']
                ]);
            } else {
                error_log("Failed to create pet: " . ($result['error'] ?? 'Unknown error'));
                return $this->sendJsonResponse(false, $result['error'] ?? 'Failed to create pet');
            }
            
        } catch (Exception $e) {
            error_log("Error in createPet: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while creating the pet: ' . $e->getMessage());
        }
    }
}
