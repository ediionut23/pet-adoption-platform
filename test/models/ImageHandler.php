<?php
class ImageHandler {
    private $conn;
    private $uploadPath = '../uploads/';
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    private $maxFileSize = 5242880; // 5MB
    
    public function __construct($db) {
        $this->conn = $db;
        
        if (!file_exists($this->uploadPath)) {
            if (!mkdir($this->uploadPath, 0777, true)) {
                error_log("Failed to create upload directory: " . $this->uploadPath);
            }
        }
        
        $petPath = $this->uploadPath . 'pets/';
        if (!file_exists($petPath)) {
            if (!mkdir($petPath, 0777, true)) {
                error_log("Failed to create pets upload directory: " . $petPath);
            }
        }
    }
    
    public function uploadPetImages($pet_id, $files) {
        $uploadedFiles = [];
        $errors = [];
        
        error_log("Starting uploadPetImages for pet_id: " . $pet_id);
        
        if (!isset($files['name']) || empty($files['name'][0])) {
            error_log("No files provided in upload");
            return ['success' => false, 'errors' => ['No files uploaded']];
        }
        
        error_log("Processing " . count($files['name']) . " files");
        
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileType = $files['type'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileError = $files['error'][$i];
            $fileSize = $files['size'][$i];
            
            error_log("Processing file: " . $fileName . ", type: " . $fileType . ", size: " . $fileSize);
            
            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = "Upload error for file: $fileName (Error code: $fileError)";
                error_log("File upload error for $fileName: Error code $fileError");
                continue;
            }
            
            if (!in_array($fileType, $this->allowedTypes)) {
                $errors[] = "Invalid file type for: $fileName (Type: $fileType)";
                error_log("Invalid file type for $fileName: $fileType");
                continue;
            }
            
            if ($fileSize > $this->maxFileSize) {
                $errors[] = "File too large: $fileName ($fileSize bytes)";
                error_log("File too large: $fileName ($fileSize bytes)");
                continue;
            }
            
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'pet_' . $pet_id . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $this->uploadPath . 'pets/' . $newFileName;
            
            error_log("Target path for file: " . $targetPath);
            
            if (move_uploaded_file($fileTmpName, $targetPath)) {
                error_log("File moved successfully to: " . $targetPath);
                
                $relativePath = 'uploads/pets/' . $newFileName;
                $mediaType = 'photo'; 
                
                $query = "INSERT INTO media (pet_id, url, type) VALUES (:pet_id, :url, :type)";
                $stmt = oci_parse($this->conn, $query);
                
                if ($stmt) {
                    error_log("Successfully parsed media insert query");
                    
                    oci_bind_by_name($stmt, ":pet_id", $pet_id);
                    oci_bind_by_name($stmt, ":url", $relativePath);
                    oci_bind_by_name($stmt, ":type", $mediaType);
                    
                    if (oci_execute($stmt)) {
                        error_log("Successfully executed media insert query");
                        $uploadedFiles[] = $relativePath;
                    } else {
                        $e = oci_error($stmt);
                        $errorMsg = "Database error for file: $fileName - " . $e['message'];
                        $errors[] = $errorMsg;
                        error_log($errorMsg);
                        
                        if (file_exists($targetPath)) {
                            if (unlink($targetPath)) {
                                error_log("Deleted file due to database error: " . $targetPath);
                            } else {
                                error_log("Failed to delete file after database error: " . $targetPath);
                            }
                        }
                    }
                    
                    oci_free_statement($stmt);
                } else {
                    $e = oci_error($this->conn);
                    $errorMsg = "Database parse error for file: $fileName - " . $e['message'];
                    $errors[] = $errorMsg;
                    error_log($errorMsg);
                    
                    if (file_exists($targetPath)) {
                        if (unlink($targetPath)) {
                            error_log("Deleted file due to parse error: " . $targetPath);
                        } else {
                            error_log("Failed to delete file after parse error: " . $targetPath);
                        }
                    }
                }
            } else {
                $errorMsg = "Could not move file: $fileName to $targetPath";
                $errors[] = $errorMsg;
                error_log($errorMsg);
            }
        }
        
        $success = !empty($uploadedFiles);
        error_log("Upload complete. Success: " . ($success ? "true" : "false") . 
                 ", Files uploaded: " . count($uploadedFiles) . 
                 ", Errors: " . count($errors));
        
        return [
            'success' => $success,
            'files' => $uploadedFiles,
            'errors' => $errors
        ];
    }
    
    public function deleteImage($media_id) {
        $query = "SELECT url FROM media WHERE id = :media_id";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            return false;
        }
        
        oci_bind_by_name($stmt, ":media_id", $media_id);
        
        if (!oci_execute($stmt)) {
            oci_free_statement($stmt);
            return false;
        }
        
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        if (!$row) {
            return false;
        }
        
        $filePath = '../' . $row['URL'];
        
        $query = "DELETE FROM media WHERE id = :media_id";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            return false;
        }
        
        oci_bind_by_name($stmt, ":media_id", $media_id);
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if ($result) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return true;
        }
        
        return false;
    }
    
    public function getImagesByPetId($pet_id) {
        $query = "SELECT * FROM media WHERE pet_id = :pet_id";
        $stmt = oci_parse($this->conn, $query);
        
        if (!$stmt) {
            return [];
        }
        
        oci_bind_by_name($stmt, ":pet_id", $pet_id);
        
        if (!oci_execute($stmt)) {
            oci_free_statement($stmt);
            return [];
        }
        
        $images = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $images[] = $row;
        }
        
        oci_free_statement($stmt);
        return $images;
    }
}