<?php
$base_path = dirname(dirname(__FILE__)) . '/';
require_once $base_path . 'models/ImageHandler.php';

error_log("PHP Upload Configuration: " . json_encode([
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir'),
    'memory_limit' => ini_get('memory_limit')
]));

class Pet {
    private $conn;
    private $imageHandler;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->imageHandler = new ImageHandler($db);
    }
    
    public function getPetById($id) {
        $query = "SELECT * FROM pets WHERE id = :pet_id";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            throw new Exception("Could not parse query");
        }
        
        oci_bind_by_name($stmt, ":pet_id", $id);
        $execute = oci_execute($stmt);
        
        if (!$execute) {
            throw new Exception("Could not execute query");
        }
        
        $pet = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        return $pet;
    }    public function getPetMedia($pet_id) {
        $media_query = "SELECT * FROM media WHERE pet_id = :pet_id";
        $media_stmt = oci_parse($this->conn, $media_query);
        
        if (!$media_stmt) {
            throw new Exception("Could not parse media query");
        }
        
        oci_bind_by_name($media_stmt, ":pet_id", $pet_id);
        $media_execute = oci_execute($media_stmt);
        
        if (!$media_execute) {
            throw new Exception("Could not execute media query");
        }
        
        $media = array();
        while ($row = oci_fetch_assoc($media_stmt)) {
            $media[] = $row;
        }
        
        oci_free_statement($media_stmt);
        
        return $media;
    }public function createPet($data) {
        try {            $query = "INSERT INTO pets (
                name, species, breed, age, gender, health_status, description,
                available_for_adoption, adoption_address, owner_id, personality_description,
                activity_description, diet_description, household_activity,
                household_environment, other_pets, color, marime, spayed_neutered,
                time_at_current_home, reason_for_rehoming, flea_treatment,
                current_owner_description, latitude, longitude
            ) VALUES (
                :name, :species, :breed, :age, :gender, :health_status, :description,
                1, :adoption_address, :owner_id, :personality_description,
                :activity_description, :diet_description, :household_activity,
                :household_environment, :other_pets, :color, :marime, :spayed_neutered,
                :time_at_current_home, :reason_for_rehoming, :flea_treatment,
                :current_owner_description, :latitude, :longitude
            ) RETURNING id INTO :inserted_id";

            $stmt = oci_parse($this->conn, $query);
            if (!$stmt) {
                $e = oci_error($this->conn);                throw new Exception("Could not parse create pet query: " . $e['message']);
            }

            $requiredFields = [
                'health_status' => 'Good', 
                'description' => '', 
                'adoption_address' => '',
                'personality_description' => '', 
                'activity_description' => '',
                'diet_description' => '', 
                'household_activity' => '', 
                'household_environment' => '',
                'other_pets' => '', 
                'spayed_neutered' => 0, 
                'time_at_current_home' => '',
                'reason_for_rehoming' => '', 
                'flea_treatment' => '', 
                'current_owner_description' => ''
            ];

            foreach ($requiredFields as $field => $defaultValue) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    $data[$field] = $defaultValue;
                }
            }

            foreach ($data as $key => $value) {
                if ($key !== 'pet_images') {                    oci_bind_by_name($stmt, ":{$key}", $data[$key]);
                }
            }            $inserted_id = 0;
            oci_bind_by_name($stmt, ":inserted_id", $inserted_id, -1, SQLT_INT);
            $execute = oci_execute($stmt);
            
            if (!$execute) {
                $e = oci_error($stmt);                throw new Exception("Could not create pet: " . $e['message']);
            }            oci_free_statement($stmt);
            
            return $inserted_id;
        } catch (Exception $e) {
            error_log("Error in createPet: " . $e->getMessage());
            throw $e;
        }
    }private function beginTransaction() {
        $stmt = oci_parse($this->conn, "BEGIN NULL; END;");
        return oci_execute($stmt, OCI_NO_AUTO_COMMIT);
    }    
    private function getCoordinatesFromAddress($address) {
        $apiKey = 'YOUR_GOOGLE_MAPS_API_KEY';
        
        if (stripos($address, 'romania') === false && stripos($address, 'românia') === false) {
            $address .= ', Romania';
        }
        
        $address = str_replace(
            ['ă', 'â', 'î', 'ș', 'ț', 'Ă', 'Â', 'Î', 'Ș', 'Ț'],
            ['a', 'a', 'i', 's', 't', 'A', 'A', 'I', 'S', 'T'],
            $address
        );
        
        $address = urlencode($address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}&region=ro";
        
        $response = file_get_contents($url);
        $data = json_decode($response, true);   
        
        if ($data['status'] === 'OK') {
            $isRomania = false;
            foreach ($data['results'][0]['address_components'] as $component) {
                if (in_array('country', $component['types']) && $component['short_name'] === 'RO') {
                    $isRomania = true;
                    break;
                }
            }
            
            if ($isRomania) {
                return [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng']
                ];
            }
        }
        
        return [
            'lat' => 47.1585,
            'lng' => 27.6014
        ];
    }

    public function getAllPetsWithCoordinates() {
        $query = "SELECT id, name, species, latitude, longitude FROM pets WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            throw new Exception("Could not parse query for pet coordinates");
        }
        
        $execute = oci_execute($stmt);
        
        if (!$execute) {
            $e = oci_error($stmt);
            throw new Exception("Could not execute pet coordinates query: " . $e['message']);
        }
        
        $pets = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $pets[] = $row;
        }
        
        oci_free_statement($stmt);
        
        return $pets;
    }    
    public function uploadPetImages($pet_id, $files) {
        try {
            if (!isset($files['name']) || empty($files['name'][0])) {
                return ['success' => false, 'errors' => ['No files uploaded']];
            }
            
            $mainUploadDir = dirname(__DIR__) . '/uploads/';
            
            if (!file_exists($mainUploadDir)) {
                if (!mkdir($mainUploadDir, 0777, true)) {
                    return ['success' => false, 'errors' => ['Failed to create uploads directory']];
                }
            }
            
            $uploadedFiles = [];
            $errors = [];
            
            for ($i = 0; $i < count($files['name']); $i++) {
                $fileName = $files['name'][$i];
                $tmpName = $files['tmp_name'][$i];
                $error = $files['error'][$i];
                
                if ($error === UPLOAD_ERR_OK) {
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = 'pet_' . $pet_id . '_' . uniqid() . '.' . $fileExtension;
                    $targetPath = $mainUploadDir . $newFileName;
                    $relativePath = 'uploads/' . $newFileName;
                    
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        chmod($targetPath, 0644);
                        
                        $query = "INSERT INTO media (pet_id, url, type) VALUES (:pet_id, :url, 'photo')";
                        $stmt = oci_parse($this->conn, $query);
                        
                        if ($stmt) {
                            oci_bind_by_name($stmt, ":pet_id", $pet_id);
                            oci_bind_by_name($stmt, ":url", $relativePath);
                            
                            if (oci_execute($stmt)) {
                                $uploadedFiles[] = $relativePath;
                            } else {
                                $errors[] = "Database error for file: $fileName";
                                if (file_exists($targetPath)) {
                                    unlink($targetPath);
                                }
                            }
                            oci_free_statement($stmt);
                        }
                    } else {
                        $errors[] = "Failed to move file: $fileName";
                    }
                } else {
                    $errors[] = "Upload error for file: $fileName (Error code: $error)";
                }
            }
            
            return [
                'success' => !empty($uploadedFiles),
                'files' => $uploadedFiles,
                'errors' => $errors
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function getPetsByUserId($userId) {
        $query = "SELECT * FROM pets WHERE owner_id = :user_id ORDER BY id DESC";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            throw new Exception("Could not parse query");
        }
        
        oci_bind_by_name($stmt, ":user_id", $userId);
        $execute = oci_execute($stmt);
        
        if (!$execute) {
            throw new Exception("Could not execute query");
        }
        
        $pets = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $pets[] = $row;
        }
        
        oci_free_statement($stmt);
        
        return $pets;
    }    public function getUserPets($userId) {
        try {
            if (!$userId) {
                throw new Exception("Invalid user ID provided");
            }

            $userQuery = "SELECT rol FROM users WHERE id = :user_id";
            $userStmt = oci_parse($this->conn, $userQuery);
            if (!$userStmt) {
                throw new Exception("Database error while preparing user query");
            }
            
            oci_bind_by_name($userStmt, ":user_id", $userId);
            $executeUser = oci_execute($userStmt);
            
            if (!$executeUser) {
                throw new Exception("Database error while fetching user role");
            }
            
            $userRow = oci_fetch_assoc($userStmt);
            oci_free_statement($userStmt);
            
            $isAdmin = $userRow && strtolower($userRow['ROL']) === 'admin';

            $query = "SELECT 
                        p.id, p.name, p.species, p.breed, p.age, 
                        p.gender, p.health_status, p.description,
                        p.available_for_adoption, p.adoption_address,
                        p.owner_id, p.color, p.marime,
                        p.spayed_neutered
                     FROM pets p ";
            
            if (!$isAdmin) {
                $query .= "WHERE p.owner_id = :user_id ";
            }
            
            $query .= "ORDER BY p.id DESC";
                     
            $stmt = oci_parse($this->conn, $query);
            
            if (!$stmt) {
                $error = oci_error($this->conn);
                error_log("Parse error in getUserPets: " . print_r($error, true));
                throw new Exception("Database error while preparing query");
            }
            
            if (!$isAdmin) {
                $userIdNum = (int)$userId;
                oci_bind_by_name($stmt, ":user_id", $userIdNum);
            }
            
            $execute = oci_execute($stmt);
            
            if (!$execute) {
                $error = oci_error($stmt);
                error_log("Execute error in getUserPets: " . print_r($error, true));
                throw new Exception("Database error while fetching pets");
            }
            
            $pets = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $row['ID'] = (int)$row['ID'];
                $row['AGE'] = is_numeric($row['AGE']) ? (int)$row['AGE'] : null;
                $row['OWNER_ID'] = (int)$row['OWNER_ID'];
                $row['SPAYED_NEUTERED'] = (bool)$row['SPAYED_NEUTERED'];
                $row['AVAILABLE_FOR_ADOPTION'] = (bool)$row['AVAILABLE_FOR_ADOPTION'];
                
                $pets[] = $row;
            }
            
            oci_free_statement($stmt);
            
            return $pets;
            
        } catch (Exception $e) {
            error_log("Error in getUserPets model: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function deletePet($petId) {
        try {
            $petQuery = "SELECT owner_id FROM pets WHERE id = :pet_id";
            $petStmt = oci_parse($this->conn, $petQuery);
            if (!$petStmt) {
                throw new Exception("Could not parse pet query");
            }
            oci_bind_by_name($petStmt, ":pet_id", $petId);
            $execute = oci_execute($petStmt);
            if (!$execute) {
                throw new Exception("Could not execute pet query");
            }
            $pet = oci_fetch_assoc($petStmt);
            oci_free_statement($petStmt);

            if (!$pet) {
                throw new Exception("Pet not found");
            }          
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $currentUserId = $_SESSION['user_id'] ?? null;
            if (!$currentUserId) {
                throw new Exception("User not authenticated");
            }

            $userQuery = "SELECT rol FROM users WHERE id = :user_id";
            $userStmt = oci_parse($this->conn, $userQuery);
            if (!$userStmt) {
                throw new Exception("Could not parse user query");
            }
            oci_bind_by_name($userStmt, ":user_id", $currentUserId);
            $executeUser = oci_execute($userStmt);
            if (!$executeUser) {
                throw new Exception("Could not execute user query");
            }
            $userRow = oci_fetch_assoc($userStmt);
            oci_free_statement($userStmt);

            $isAdmin = $userRow && strtolower($userRow['ROL']) === 'admin';            // Check if user has permission to delete
            if (!$isAdmin && intval($pet['OWNER_ID']) !== intval($currentUserId)) {
                throw new Exception("You do not have permission to delete this pet");
            }            // Delete related records from all dependent tables in the correct order
            $dependentTables = [
                'messages',           // Messages referencing the pet
                'media',             // Media files
                'adoption_form',      // Adoption forms
                'adoptions'          // Adoption records (must be after adoption_form)
            ];

            foreach ($dependentTables as $table) {
                $query = "DELETE FROM {$table} WHERE pet_id = :pet_id";
                $stmt = oci_parse($this->conn, $query);
                
                if (!$stmt) {
                    throw new Exception("Could not parse delete query for {$table}");
                }
                
                oci_bind_by_name($stmt, ":pet_id", $petId);
                $execute = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
                
                if (!$execute) {
                    $e = oci_error($stmt);
                    oci_rollback($this->conn);
                    throw new Exception("Could not delete from {$table}: " . $e['message']);
                }
                
                oci_free_statement($stmt);
            }

            $query = "DELETE FROM pets WHERE id = :pet_id";
            $stmt = oci_parse($this->conn, $query);
            
            if (!$stmt) {
                oci_rollback($this->conn);
                throw new Exception("Could not parse delete pet query");
            }
            
            oci_bind_by_name($stmt, ":pet_id", $petId);
            $execute = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            
            if (!$execute) {
                $e = oci_error($stmt);
                oci_rollback($this->conn);
                throw new Exception("Could not delete pet: " . $e['message']);
            }
            
            oci_free_statement($stmt);

            $commit = oci_commit($this->conn);
            if (!$commit) {
                oci_rollback($this->conn);
                throw new Exception("Could not commit transaction");
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function deleteAllPetMedia($petId) {
        $media = $this->getPetMedia($petId);
        
        foreach ($media as $item) {
            if (isset($item['URL']) && !empty($item['URL'])) {
                $filePath = dirname(dirname(__FILE__)) . '/' . $item['URL'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $query = "DELETE FROM media WHERE pet_id = :pet_id";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            throw new Exception("Could not parse delete media query");
        }
        
        oci_bind_by_name($stmt, ":pet_id", $petId);
        $execute = oci_execute($stmt);
        
        if (!$execute) {
            throw new Exception("Could not delete pet media");
        }
        
        oci_free_statement($stmt);
        return true;
    }
      private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return "No error, file upload successful";
            case UPLOAD_ERR_INI_SIZE:
                return "File exceeds the upload_max_filesize directive in php.ini";
            case UPLOAD_ERR_FORM_SIZE:
                return "File exceeds the MAX_FILE_SIZE directive in the HTML form";
            case UPLOAD_ERR_PARTIAL:
                return "File was only partially uploaded";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION:
                return "A PHP extension stopped the file upload";
            default:
                return "Unknown upload error";
        }
    }
      private function logUploadConfiguration() {
        $uploadConfig = array(
            'file_uploads' => ini_get('file_uploads'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'memory_limit' => ini_get('memory_limit')
        );
        
        error_log("PHP Upload Configuration: " . json_encode($uploadConfig));
    }
}