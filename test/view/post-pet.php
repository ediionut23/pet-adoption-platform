<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

require_once '../controllers/PetController.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['return_to'] = 'post-pet.php';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PetController();
    
    $_POST['owner_id'] = $_SESSION['user_id'];
    
    if (!isset($_POST['description'])) {
        $_POST['description'] = $_POST['personality_description'];
    }
    
    $result = $controller->createPet($_POST, $_FILES);
    
    if (isset($result['success']) && $result['success']) {
        header('Location: pet-page.php?id=' . $result['pet_id']);
        exit;
    } else {
        $error = $result['error'] ?? "An unknown error occurred";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Pet for Adoption - Pow</title>
    <link rel="stylesheet" href="../stiluri/post-pet.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    <script>
        let map;
        let marker;
        
        function initMap() {
            const defaultLocation = { lat: 45.9432, lng: 24.9668 };
            
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 7,
                center: defaultLocation,
                streetViewControl: false,
                mapTypeControl: true,
                fullscreenControl: true,
                gestureHandling: 'greedy' // Makes it easier to move the map on mobile
            });

            map.addListener("click", (event) => {
                placeMarker(event.latLng);
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(pos);
                        map.setZoom(12);
                        placeMarker(pos);
                    },
                    () => {
                        console.log("Error: The Geolocation service failed.");
                        placeMarker(defaultLocation);
                    }
                );
            } else {
                placeMarker(defaultLocation);
            }
        }

        function placeMarker(location) {
            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            document.getElementById("latitude").value = location.lat();
            document.getElementById("longitude").value = location.lng();

            marker.addListener("dragend", (event) => {
                const newPos = marker.getPosition();
                document.getElementById("latitude").value = newPos.lat();
                document.getElementById("longitude").value = newPos.lng();
            });

            map.panTo(location);
        }

        function handleMapError() {
            const mapDiv = document.getElementById("map");
            mapDiv.innerHTML = '<div style="padding: 20px; text-align: center; background: #f8d7da; color: #721c24; border-radius: 10px;">Sorry, there was an error loading Google Maps. Please try refreshing the page.</div>';
        }

        window.initMap = initMap;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap&v=weekly&region=RO" async defer onerror="handleMapError()"></script>
    <style>
        .back-button {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            background-color: #ff5a00;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .back-button:hover {
            background-color: #ff7a30;
        }

        .back-icon {
            font-size: 1.2em;
        }

        .post-pet-container {
            margin-top: 80px;
        }

        .file-input-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }

        .file-input-container input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .custom-file-input {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-family: 'Josefin Sans', sans-serif;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .custom-file-input:hover {
            background-color: #34495e;
        }

        .file-input-container small {
            display: block;
            margin-top: 5px;
            color: #666;
        }

        .submit-button {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 30px auto;
            padding: 12px 24px;
            background-color: #ff5a00;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-family: 'Josefin Sans', sans-serif;
            font-size: 1.1em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .submit-button:hover {
            background-color: #ff7a30;
            transform: translateY(-2px);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        /* Stilizare pentru textul cu fișierele selectate */
        .selected-files {
            margin-top: 8px;
            font-size: 0.9em;
            color: #666;
        }

        /* Map container styles */
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 2px solid #ddd;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        #map:hover {
            border-color: #f16a16;
        }        /* Make sure the map controls are clickable */
        .gm-style .gm-style-iw {
            z-index: 1000;
        }

        /* Image preview container */
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .image-preview {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 8px;
            border: 2px solid #ddd;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            color: #ff0000;
            border: none;
        }

        .remove-image:hover {
            background-color: rgba(255, 255, 255, 0.9);
        }
    </style>    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('pet_images');
            const previewContainer = document.getElementById('image-preview-container');
            let selectedFiles = []; // Array to keep track of selected files
            
            previewContainer.innerHTML = '';
            
            input.addEventListener('change', function() {
                const newFiles = Array.from(this.files);
                
                newFiles.forEach(file => {
                    selectedFiles.push(file);
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'image-preview';
                        previewDiv.dataset.fileName = file.name;
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button'; // Important to prevent form submission
                        removeBtn.className = 'remove-image';
                        removeBtn.textContent = '×';
                        removeBtn.addEventListener('click', function() {
                            previewContainer.removeChild(previewDiv);
                            
                            selectedFiles = selectedFiles.filter(f => f.name !== file.name);
                            
                            updateFormFiles();
                        });
                        
                        previewDiv.appendChild(img);
                        previewDiv.appendChild(removeBtn);
                        previewContainer.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                });
                
                updateFormFiles();
                
                this.value = '';
            });
            
            function updateFormFiles() {
                if (selectedFiles.length === 0) {
                    // No files selected, just clear the input
                    input.value = '';
                    return;
                }
                
                // Create a new DataTransfer object
                const dataTransfer = new DataTransfer();
                
                // Add all currently selected files
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                
                // Set the input's files to our DataTransfer object
                input.files = dataTransfer.files;
            }
            
            // Handle form submission
            const form = document.querySelector('.post-pet-form');
            form.addEventListener('submit', function(e) {
                // Make sure we have the latest files in the form input
                updateFormFiles();
                
                // Log the number of files being submitted
                console.log(`Submitting ${input.files.length} files`);
            });
        });
    </script>
</head>
<body>
    <a href="homepage.php" class="back-button">
        <span class="back-icon">←</span>
        <span>Back</span>
    </a>

    <main class="post-pet-container">
        <h1>Post Your Pet for Adoption</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="post-pet-form">
            <div class="form-section">
                <h2>Basic Information</h2>
                <div class="form-group">
                    <label for="name">Pet's Name:</label>
                    <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="species">Species:</label>
                    <select id="species" name="species" required>
                        <option value="">Select Species</option>
                        <option value="Dog" <?php echo (isset($_POST['species']) && $_POST['species'] == 'Dog') ? 'selected' : ''; ?>>Dog</option>
                        <option value="Cat" <?php echo (isset($_POST['species']) && $_POST['species'] == 'Cat') ? 'selected' : ''; ?>>Cat</option>
                        <option value="Bird" <?php echo (isset($_POST['species']) && $_POST['species'] == 'Bird') ? 'selected' : ''; ?>>Bird</option>
                        <option value="Other" <?php echo (isset($_POST['species']) && $_POST['species'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="breed">Breed:</label>
                    <input type="text" id="breed" name="breed" required value="<?php echo isset($_POST['breed']) ? htmlspecialchars($_POST['breed']) : ''; ?>">
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="age">Age (years):</label>
                        <input type="number" id="age" name="age" min="0" step="0.5" required value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
                    </div>

                    <div class="form-group half">
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" required value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="marime">Size:</label>
                    <select id="marime" name="marime" required>
                        <option value="">Select Size</option>
                        <option value="Small" <?php echo (isset($_POST['marime']) && $_POST['marime'] == 'Small') ? 'selected' : ''; ?>>Small</option>
                        <option value="Medium" <?php echo (isset($_POST['marime']) && $_POST['marime'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="Large" <?php echo (isset($_POST['marime']) && $_POST['marime'] == 'Large') ? 'selected' : ''; ?>>Large</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h2>Health & Care</h2>
                <div class="form-group">
                    <label for="health_status">Health Status:</label>
                    <textarea id="health_status" name="health_status" required><?php echo isset($_POST['health_status']) ? htmlspecialchars($_POST['health_status']) : ''; ?></textarea>
                </div>

                <div class="form-group checkbox">
                    <input type="checkbox" id="spayed_neutered" name="spayed_neutered" <?php echo isset($_POST['spayed_neutered']) ? 'checked' : ''; ?>>
                    <label for="spayed_neutered">Spayed/Neutered</label>
                </div>

                <div class="form-group checkbox">
                    <input type="checkbox" id="flea_treatment" name="flea_treatment" <?php echo isset($_POST['flea_treatment']) ? 'checked' : ''; ?>>
                    <label for="flea_treatment">Flea Treatment</label>
                </div>
            </div>

            <div class="form-section">
                <h2>Personality & Behavior</h2>
                <div class="form-group">
                    <label for="personality_description">Personality Description:</label>
                    <textarea id="personality_description" name="personality_description" required><?php echo isset($_POST['personality_description']) ? htmlspecialchars($_POST['personality_description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="activity_description">Activity Level & Exercise Needs:</label>
                    <textarea id="activity_description" name="activity_description" required><?php echo isset($_POST['activity_description']) ? htmlspecialchars($_POST['activity_description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="diet_description">Diet & Feeding Habits:</label>
                    <textarea id="diet_description" name="diet_description" required><?php echo isset($_POST['diet_description']) ? htmlspecialchars($_POST['diet_description']) : ''; ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Current Living Situation</h2>
                <div class="form-group">
                    <label for="household_activity">Household Activity Level:</label>
                    <select id="household_activity" name="household_activity" required>
                        <option value="">Select Activity Level</option>
                        <option value="Low" <?php echo (isset($_POST['household_activity']) && $_POST['household_activity'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                        <option value="Moderate" <?php echo (isset($_POST['household_activity']) && $_POST['household_activity'] == 'Moderate') ? 'selected' : ''; ?>>Moderate</option>
                        <option value="High" <?php echo (isset($_POST['household_activity']) && $_POST['household_activity'] == 'High') ? 'selected' : ''; ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="household_environment">Living Environment:</label>
                    <select id="household_environment" name="household_environment" required>
                        <option value="">Select Environment</option>
                        <option value="Apartment" <?php echo (isset($_POST['household_environment']) && $_POST['household_environment'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                        <option value="House with yard" <?php echo (isset($_POST['household_environment']) && $_POST['household_environment'] == 'House with yard') ? 'selected' : ''; ?>>House with yard</option>
                        <option value="Rural/Farm" <?php echo (isset($_POST['household_environment']) && $_POST['household_environment'] == 'Rural/Farm') ? 'selected' : ''; ?>>Rural/Farm</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="other_pets">Other Pets in Household:</label>
                    <input type="text" id="other_pets" name="other_pets" required value="<?php echo isset($_POST['other_pets']) ? htmlspecialchars($_POST['other_pets']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="time_at_current_home">Time at Current Home:</label>
                    <input type="text" id="time_at_current_home" name="time_at_current_home" required value="<?php echo isset($_POST['time_at_current_home']) ? htmlspecialchars($_POST['time_at_current_home']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="reason_for_rehoming">Reason for Rehoming:</label>
                    <textarea id="reason_for_rehoming" name="reason_for_rehoming" required><?php echo isset($_POST['reason_for_rehoming']) ? htmlspecialchars($_POST['reason_for_rehoming']) : ''; ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Additional Information</h2>
                <div class="form-group">
                    <label for="current_owner_description">Your Description of the Pet:</label>
                    <textarea id="current_owner_description" name="current_owner_description" required><?php echo isset($_POST['current_owner_description']) ? htmlspecialchars($_POST['current_owner_description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="adoption_address">Adoption Location:</label>
                    <input type="text" id="adoption_address" name="adoption_address" required value="<?php echo isset($_POST['adoption_address']) ? htmlspecialchars($_POST['adoption_address']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Set Location on Map:</label>
                    <div id="map" style="height: 400px; width: 100%; margin-bottom: 20px; border-radius: 10px;"></div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>                <div class="form-group">
                    <label for="pet_images">Pet Photos:</label>
                    <div class="file-input-container">
                        <input type="file" id="pet_images" name="pet_images[]" accept="image/*" multiple>
                        <label for="pet_images" class="custom-file-input">Choose Files</label>
                        <small>You can select multiple images</small>
                    </div>
                    <div id="image-preview-container" class="image-preview-container"></div>
                </div>
            </div>

            <button type="submit" class="submit-button">Post for Adoption</button>
        </form>
    </main>
</body>
</html>
