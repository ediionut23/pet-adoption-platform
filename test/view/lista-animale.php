<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../stiluri/lista-animale.css" />
    <title>Pow - Pet Adoption</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
  </head>
  <body>
    <header>
      <div class="header-content">
        <a href="homepage.php" class="back-btn">Back</a>
        <a href="homepage.php" class="logo"><h1>Pow</h1></a>
        <a href="profile.php" class="profile-button">
          <img src="../stiluri/imagini/profileicon.png" alt="profile-button" />
        </a>
      </div>
    </header>
    <div class="wrapper">
      <div class="main-container">
        <h2>Filters:</h2>
        <form method="GET">
          <div class="filters">
            <div class="filter-group">
              <label for="type">Type:</label>              <select id="type" name="animal_type">
                <option value="">Select...</option>
                <option value="cat" <?php echo ($_GET['animal_type'] ?? '') === 'cat' ? 'selected' : ''; ?>>Cat</option>
                <option value="dog" <?php echo ($_GET['animal_type'] ?? '') === 'dog' ? 'selected' : ''; ?>>Dog</option>
                <option value="bird" <?php echo ($_GET['animal_type'] ?? '') === 'bird' ? 'selected' : ''; ?>>Bird</option>
                <option value="fish" <?php echo ($_GET['animal_type'] ?? '') === 'fish' ? 'selected' : ''; ?>>Fish</option>
                <option value="reptile" <?php echo ($_GET['animal_type'] ?? '') === 'reptile' ? 'selected' : ''; ?>>Reptile</option>
              </select>
            </div>
            <div class="filter-group">
              <label for="age">Age:</label>
              <select id="age" name="age">
                <option value="">Select..</option>
                <option value="young">Young</option>
                <option value="adult">Adult</option>
                <option value="senior">Senior</option>
              </select>
            </div>
            <div class="filter-group">
              <label for="weight">Weight:</label>
              <select id="weight" name="weight">
                <option value="">Select..</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
            </div>
            <div class="filter-group">
              <label for="sex">Sex:</label>
              <select id="sex" name="sex">
                <option value="">Select..</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
          </div>
          <div class="filter-buttons">
            <button type="submit" class="filter-btn">Filter</button>
            <button type="reset" class="reset-btn" onclick="window.location.href='lista-animale.php'">Reset</button>
          </div>
        </form>        <div class="cards-container" id="animals-container">
          <div class="loading">Loading animals...</div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAnimals();
            
            const filterForm = document.querySelector('form');
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadAnimals();
            });
            
            document.querySelector('.reset-btn').addEventListener('click', function(e) {
                e.preventDefault();
                filterForm.reset();
                loadAnimals();
            });
            
            function loadAnimals() {
                const container = document.getElementById('animals-container');
                container.innerHTML = '<div class="loading">Loading animals...</div>';
                const animalType = document.getElementById('type').value;
                const gender = document.getElementById('sex').value;
                const age = document.getElementById('age').value;
                const size = document.getElementById('weight').value;                /
                let queryParams = new URLSearchParams();
                
                if (animalType) queryParams.append('animal_type', animalType);
                if (gender) queryParams.append('sex', gender);
                if (age) queryParams.append('age', age);
                if (size) queryParams.append('weight', size);
                const apiUrl = `../public/api.php?type=pets&action=list${queryParams.toString() ? '&' + queryParams.toString() : ''}`;
                console.log('Fetching URL:', apiUrl); 
                  fetch(apiUrl)
                    .then(response => response.json())                    .then(data => {
                        console.log('API Response:', data);
                        
                        if (data.success) {
                            if (data.animals.length === 0) {
                                container.innerHTML = '<div class="no-results">No animals found matching your criteria.</div>';
                            } else {
                                container.innerHTML = '';
                                data.animals.forEach(animal => {
                                    const card = document.createElement('a');
                                    card.className = 'card-link';
                                    card.href = `pet-page.php?id=${animal.id}`;
                                    
                                    const statusClass = animal.available_for_adoption ? 'available' : 'not-available';
                                    const statusText = animal.available_for_adoption ? 'Available for Adoption' : 'Not Available';
                                    
                                    card.innerHTML = `
                                        <div class="card">
                                            <h3>${animal.name}</h3>
                                            <img src="${animal.image}" alt="${animal.name}" />
                                            <p>Type: ${animal.species}</p>
                                            ${animal.age ? `<p>Age: ${animal.age} years</p>` : ''}
                                            ${animal.breed ? `<p>Breed: ${animal.breed}</p>` : ''}
                                            ${animal.gender ? `<p>Sex: ${animal.gender}</p>` : ''}
                                            <div class="adoption-status ${statusClass}">${statusText}</div>
                                        </div>
                                    `;
                                    
                                    container.appendChild(card);
                                });
                            }
                        } else {
                            container.innerHTML = `<div class="error">Error loading animals: ${data.message}</div>`;
                        }
                    })                    .catch(error => {
                        container.innerHTML = '<div class="error">Error connecting to server. Please try again later.</div>';
                        console.error('Error loading animals:', error);
                        if (error.response) {
                            console.error('Response status:', error.response.status);
                            error.response.text().then(text => {
                                console.error('Response text:', text);
                            });
                        }
                    });                // Update URL with filter parameters (for bookmarking/sharing - using the same parameter names as the API)
                const pageUrl = new URL(window.location);
                if (animalType) pageUrl.searchParams.set('animal_type', animalType); else pageUrl.searchParams.delete('animal_type');
                if (gender) pageUrl.searchParams.set('sex', gender); else pageUrl.searchParams.delete('sex');
                if (age) pageUrl.searchParams.set('age', age); else pageUrl.searchParams.delete('age');
                if (size) pageUrl.searchParams.set('weight', size); else pageUrl.searchParams.delete('weight');
                window.history.pushState({}, '', pageUrl);
            }
        });
        </script>
      </div>
    </div>
  </body>
</html>
