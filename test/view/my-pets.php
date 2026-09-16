<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posted Pets - Pow</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Josefin Sans', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
        }

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

        .container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .pet-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .pet-card:hover {
            transform: translateY(-5px);
        }

        .pet-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .pet-info {
            padding: 15px;
            text-align: center;
        }

        .pet-name {
            font-size: 1.2em;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            width: 100%;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .no-pets {
            text-align: center;
            color: #7f8c8d;
            font-size: 1.2em;
            margin-top: 50px;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 50px auto;
            width: 100%;
        }
        .loading-spinner::after {
            content: "";
            width: 50px;
            height: 50px;
            border: 6px solid #e0e0e0;
            border-top: 6px solid #ff5a00;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <a href="profile.php" class="back-button">
        <span class="back-icon">←</span>
        <span>Back</span>
    </a>

    <div class="container">
        <h1>My Posted Pets</h1>        <div class="pets-grid" id="pets-container">
            <div id="loading" class="loading-spinner"></div>
            <div class="no-pets" id="no-pets" style="display: none;">
                <p>You haven't posted any pets yet.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchUserPets();
        });
        
        async function fetchUserPets() {
            const container = document.getElementById('pets-container');
            const loading = document.getElementById('loading');
            const noPets = document.getElementById('no-pets');
            
            try {
                const response = await fetch(`../public/api.php?type=pets&action=get_user_pets`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    loading.style.display = 'none';
                    
                    if (data.pets && data.pets.length > 0) {
                        data.pets.forEach(pet => {
                            const petCard = document.createElement('div');
                            petCard.className = 'pet-card';
                            
                            if (pet.IMAGE) {
                                const img = document.createElement('img');
                                img.src = pet.IMAGE;
                                img.alt = pet.NAME || 'Pet';
                                img.className = 'pet-image';
                                petCard.appendChild(img);
                            } else {
                                const imgPlaceholder = document.createElement('div');
                                imgPlaceholder.className = 'pet-image';
                                imgPlaceholder.style.backgroundColor = '#f0f0f0';
                                imgPlaceholder.style.display = 'flex';
                                imgPlaceholder.style.alignItems = 'center';
                                imgPlaceholder.style.justifyContent = 'center';
                                
                                const nameSpan = document.createElement('span');
                                nameSpan.style.color = '#999';
                                nameSpan.textContent = pet.NAME || 'Pet';
                                
                                imgPlaceholder.appendChild(nameSpan);
                                petCard.appendChild(imgPlaceholder);
                            }
                            
                            const infoDiv = document.createElement('div');
                            infoDiv.className = 'pet-info';
                            
                            const petName = document.createElement('h3');
                            petName.className = 'pet-name';
                            petName.textContent = pet.NAME || 'Unnamed Pet';
                            
                            const deleteBtn = document.createElement('button');
                            deleteBtn.className = 'delete-btn';
                            deleteBtn.textContent = 'Delete';
                            deleteBtn.onclick = function() { deletePet(pet.ID); };
                            
                            infoDiv.appendChild(petName);
                            infoDiv.appendChild(deleteBtn);
                            petCard.appendChild(infoDiv);
                            
                            // Add to container
                            container.appendChild(petCard);
                        });
                    } else {
                        noPets.style.display = 'block';
                    }
                } else {                    throw new Error(data.message || 'Error fetching pets');
                }
            } catch (error) {
                console.error('Error:', error);
                loading.style.display = 'none';
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                let errorMessage = error.message;
                
                try {
                    const errorData = JSON.parse(error.message);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                }
                
                errorDiv.textContent = 'Failed to load pets: ' + errorMessage;
                container.appendChild(errorDiv);
            }
        }
          function deletePet(petId) {
            if (confirm('Are you sure you want to delete this pet?')) {
                fetch(`../public/api.php?type=pets&action=delete_pet&id=${petId}`, {
                    method: 'POST',
                })
                .then(async response => {
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || `Error: ${response.status}`);
                        }
                        return data;
                    } catch (e) {
                        console.error('Server response:', text);
                        throw new Error(e.message || 'An error occurred while deleting the pet');
                    }
                })
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Error deleting pet: ' + error.message);
                });
            }
        }
    </script>
</body>
</html>