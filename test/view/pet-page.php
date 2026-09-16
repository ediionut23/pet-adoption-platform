<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pet_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pet Details - Pow</title>
    <link rel="stylesheet" href="../stiluri/pet-page.css" />
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
    <style>
        #map {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
        }
        .loading-spinner::after {
            content: "";
            width: 50px;
            height: 50px;
            border: 6px solid #e0e0e0;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        .error-message {
            color: #d9534f;
            background-color: #f9f2f2;
            border: 1px solid #d9534f;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <header class="navbar">
        <a href="lista-animale.php" class="back-btn">
            Back
        </a>

        <a href="homepage.php" class="logo">Pow</a>

        <a href="profile.php" class="profile-icon">
            <img src="../stiluri/imagini/profileicon.png" alt="Profile" />
        </a>
    </header>

    <section class="pet-banner">
        <h1 id="pet-name">Loading pet details...</h1>
    </section>
    
    <div id="error-container" style="display: none;"></div>    <div id="loading-spinner" class="loading-spinner"></div>
    
    <div id="pet-content" style="display: none;">
        <div class="pet-photos">
            <div id="photo-container" class="photo-container">
                <!-- Photos will be added here by JavaScript -->
            </div>
        </div>

        <section class="pet-info">
            <div class="description">
                <h2 id="pet-name-description">Pet Description</h2>
            </div>
            <div class="info-section">
                <h2>Personality:</h2>
                <p id="personality-description"></p>
            </div>

            <div class="info-section">
                <h2>Activity:</h2>
                <p id="activity-description"></p>
            </div>

            <div class="info-section">
                <h2>Diet:</h2>
                <p id="diet-description"></p>
            </div>
        </section>    <section class="profile-summary">
        <h2 class="section-title">Current Home Life</h2>
        <div class="profile-row">
            <span class="label">Household Activity</span>
            <span id="household-activity" class="value"></span>
        </div>
        <div class="profile-row">
            <span class="label">Household Environment</span>
            <span id="household-environment" class="value"></span>
        </div>
        <div class="profile-row">
            <span class="label">Other Pets</span>
            <span id="other-pets" class="value"></span>
        </div>

        <h2 class="section-title" id="profile-title">Profile Summary</h2>
        <div class="profile-row"><span class="label">Colour:</span><span id="color" class="value"></span></div>
        <div class="profile-row"><span class="label">Breed:</span><span id="breed" class="value"></span></div>
        <div class="profile-row"><span class="label">Sex:</span><span id="gender" class="value"></span></div>
        <div class="profile-row"><span class="label">Size:</span><span id="size" class="value"></span></div>
        <div class="profile-row"><span class="label">Age:</span><span id="age" class="value"></span></div>
        <div class="profile-row"><span class="label">Spayed/Neutred:</span><span id="spayed" class="value"></span></div>
        <div class="profile-row"><span class="label">Time at current home:</span><span id="time-at-home" class="value"></span></div>
        <div class="profile-row"><span class="label">Reason for rehoming:</span><span id="rehoming-reason" class="value"></span></div>
        <div class="profile-row"><span class="label">Flea treatment:</span><span id="flea-treatment" class="value"></span></div>
    </section>    <section class="owner-description">
        <div class="owner-description-content">
            <h2 class="section-title">Current Owner's Description</h2>
            <p id="owner-description"></p>
        </div>
        <div class="adopt-section">
            <img id="pet-main-image" class="cat-image" alt="Pet Image">
            <button id="adopt-btn" class="adopt-btn">Adopt Me</button>
            <button id="message-btn" class="message-btn">Message Owner</button>
        </div>
    </section>    <section class="location">
        <h2>Location Address</h2>
        <p id="adoption-address"></p>
        <div id="map"></div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const petId = <?php echo $pet_id; ?>;
            const userId = <?php echo $user->id; ?>;
            
            fetchPetDetails(petId);
            
            document.getElementById('adopt-btn').addEventListener('click', function() {
                window.location.href = `formular.php?pet_id=${petId}`;
            });
        });        async function fetchPetDetails(petId) {
            try {
                console.log(`Fetching pet details for ID: ${petId}`);                // Use the MVC API endpoint dedicated to this page
                const response = await fetch(`../public/api.php?type=pets&action=get_pet_page_data&id=${petId}`);
                
                if (!response.ok) {
                    console.error(`HTTP error: ${response.status} ${response.statusText}`);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                let data;
                let responseText;
                try {
                    responseText = await response.text();
                    console.log("Raw response:", responseText.substring(0, 500) + (responseText.length > 500 ? '...' : ''));
                    
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error("Failed to parse JSON:", parseError);
                    console.error("Response text:", responseText);
                    throw new Error("Invalid response format from server");
                }
                
                console.log("Parsed data:", data);
                  if (data && data.success) {
                    if (data.pet && typeof data.pet === 'object') {
                        displayPetDetails(data);
                        
                        if (data.pet.LATITUDE && data.pet.LONGITUDE) {
                            initMap(data.pet, Array.isArray(data.allPetsWithCoordinates) ? data.allPetsWithCoordinates : []);
                        } else {
                            document.getElementById("map").innerHTML = "<p style='padding: 20px; text-align: center;'>Location information unavailable</p>";
                        }
                        
                        if (data.warnings && data.warnings.length > 0) {
                            console.warn("API Warnings:", data.warnings);
                        }
                    } else {
                        showError('Received invalid pet data from server');
                    }
                } else {
                    showError(data && data.message ? data.message : 'Could not fetch pet details');
                }
            } catch (error) {
                console.error("Fetch error:", error);
                showError('Error fetching pet details: ' + error.message);
            }
        }
        
        function displayPetDetails(data) {
            const pet = data.pet;
            const media = data.media;
            const userId = <?php echo $user->id; ?>;
            
            document.getElementById('loading-spinner').style.display = 'none';
            document.getElementById('pet-content').style.display = 'block';
            
            document.title = pet.NAME + ' - Pow';
            document.getElementById('pet-name').textContent = pet.NAME;
            document.getElementById('pet-name-description').textContent = pet.NAME + ' Description';
            document.getElementById('personality-description').innerHTML = nl2br(pet.PERSONALITY_DESCRIPTION);
            document.getElementById('activity-description').innerHTML = nl2br(pet.ACTIVITY_DESCRIPTION);
            document.getElementById('diet-description').innerHTML = nl2br(pet.DIET_DESCRIPTION);
            
            document.getElementById('household-activity').textContent = pet.HOUSEHOLD_ACTIVITY;
            document.getElementById('household-environment').textContent = pet.HOUSEHOLD_ENVIRONMENT;
            document.getElementById('other-pets').textContent = pet.OTHER_PETS;
            
            document.getElementById('profile-title').textContent = pet.NAME + ' Profile Summary';
            document.getElementById('color').textContent = pet.COLOR;
            document.getElementById('breed').textContent = pet.BREED;
            document.getElementById('gender').textContent = pet.GENDER;
            document.getElementById('size').textContent = pet.MARIME;
            document.getElementById('age').textContent = pet.AGE + ' years';
            document.getElementById('spayed').textContent = pet.SPAYED_NEUTERED ? 'Yes' : 'No';
            document.getElementById('time-at-home').textContent = pet.TIME_AT_CURRENT_HOME;
            document.getElementById('rehoming-reason').textContent = pet.REASON_FOR_REHOMING;
            document.getElementById('flea-treatment').textContent = pet.FLEA_TREATMENT ? 'Yes' : 'No';
            
            document.getElementById('owner-description').innerHTML = nl2br(pet.CURRENT_OWNER_DESCRIPTION);
            document.getElementById('adoption-address').textContent = pet.ADOPTION_ADDRESS;
            
            const photoContainer = document.getElementById('photo-container');
            photoContainer.innerHTML = ''; // Clear container
            
            if (media && media.length > 0) {
                media.forEach(image => {
                    const img = document.createElement('img');
                    img.src = '../' + image.URL;
                    img.alt = pet.NAME + ' photo';
                    img.className = 'pet-photo';
                    photoContainer.appendChild(img);
                });
                
                document.getElementById('pet-main-image').src = '../' + media[0].URL;
                document.getElementById('pet-main-image').alt = pet.NAME;
            } else {
                const speciesLower = pet.SPECIES.toLowerCase();
                const img = document.createElement('img');
                img.src = `../stiluri/imagini/${speciesLower}.png`;
                img.alt = pet.SPECIES;
                img.className = 'pet-photo';
                photoContainer.appendChild(img);
                
                document.getElementById('pet-main-image').src = `../stiluri/imagini/${speciesLower}.png`;
                document.getElementById('pet-main-image').alt = pet.SPECIES;
            }
            
            const messageBtn = document.getElementById('message-btn');
            messageBtn.textContent = userId === parseInt(pet.OWNER_ID) ? 'Test Messages' : 'Message Owner';
            messageBtn.addEventListener('click', function() {
                window.location.href = `messages.php?pet_id=${pet.ID}&owner_id=${pet.OWNER_ID}`;
            });
        }
          function initMap(pet, allPets) {
            if (!pet || !pet.LATITUDE || !pet.LONGITUDE) {
                console.error("Missing pet coordinates", pet);
                document.getElementById("map").innerHTML = "<p style='padding: 20px; text-align: center;'>Location information unavailable</p>";
                return;
            }
            
            const lat = parseFloat(pet.LATITUDE);
            const lng = parseFloat(pet.LONGITUDE);
            
            if (isNaN(lat) || isNaN(lng)) {
                console.error("Invalid coordinates", { lat, lng });
                document.getElementById("map").innerHTML = "<p style='padding: 20px; text-align: center;'>Invalid location coordinates</p>";
                return;
            }
            
            const currentPet = { lat, lng };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: currentPet,
                styles: [
                    {
                        "featureType": "poi",
                        "elementType": "labels",
                        "stylers": [{ "visibility": "off" }]
                    }
                ]
            });

            const currentMarker = new google.maps.Marker({
                position: currentPet,
                map: map,
                title: pet.NAME + "'s Location",
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                }
            });

            const circle = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.15,
                map: map,
                center: currentPet,
                radius: 500
            });

            const markers = [];
            const bounds = new google.maps.LatLngBounds();

            allPets.forEach(otherPet => {
                if (otherPet.ID != pet.ID) {
                    const position = {
                        lat: parseFloat(otherPet.LATITUDE),
                        lng: parseFloat(otherPet.LONGITUDE)
                    };

                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: otherPet.NAME,
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                        }
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 10px;">
                                <h3>${otherPet.NAME}</h3>
                                <p>${otherPet.SPECIES}</p>
                                <a href="pet-page.php?id=${otherPet.ID}" style="color: blue;">Vezi detalii</a>
                            </div>
                        `
                    });

                    marker.addListener("click", () => {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                    bounds.extend(position);
                }
            });

            map.addListener("zoom_changed", () => {
                const zoom = map.getZoom();
                markers.forEach(marker => {
                    marker.setVisible(zoom <= 12);
                });
            });

            const initialZoom = map.getZoom();
            markers.forEach(marker => {
                marker.setVisible(initialZoom <= 12);
            });
        }
        
        function showError(message) {
            document.getElementById('loading-spinner').style.display = 'none';
            const errorContainer = document.getElementById('error-container');
            errorContainer.innerHTML = `<div class="error-message">${message}</div>`;
            errorContainer.style.display = 'block';
        }
        
        function nl2br(str) {
            if (typeof str !== 'string') return '';
            return str.replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
        }
    </script>
</body>
</html>
