<?php
require_once '../config/database.php';
require_once '../models/ImageHandler.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if a file was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the pet ID from the form
    $petId = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : 0;
    
    if ($petId <= 0) {
        die("Invalid pet ID");
    }

    // Check if files were uploaded
    if (!isset($_FILES['pet_images']) || empty($_FILES['pet_images']['name'][0])) {
        die("No files were uploaded");
    }

    try {
        // Get database connection
        $conn = getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Create ImageHandler instance
        $imageHandler = new ImageHandler($conn);
        
        // Upload the images
        $result = $imageHandler->uploadPetImages($petId, $_FILES['pet_images']);
        
        if ($result['success']) {
            echo "Images uploaded successfully!<br>";
            echo "Uploaded files:<br>";
            foreach ($result['files'] as $file) {
                echo "- " . htmlspecialchars($file) . "<br>";
            }
        }
        
        if (!empty($result['errors'])) {
            echo "<br>Errors:<br>";
            foreach ($result['errors'] as $error) {
                echo "- " . htmlspecialchars($error) . "<br>";
            }
        }

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Pet Images</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="number"] {
            width: 100px;
            padding: 5px;
        }
        input[type="file"] {
            margin-top: 5px;
        }
        input[type="submit"] {
            background: #f16a16;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #224453;
        }
    </style>
</head>
<body>
    <h2>Upload Pet Images</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="pet_id">Pet ID:</label>
            <input type="number" name="pet_id" id="pet_id" required min="1" value="<?php echo isset($_POST['pet_id']) ? htmlspecialchars($_POST['pet_id']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="pet_images">Select Images:</label>
            <input type="file" name="pet_images[]" id="pet_images" multiple accept="image/*" required>
        </div>
        <input type="submit" value="Upload Images">
    </form>
</body>
</html> 