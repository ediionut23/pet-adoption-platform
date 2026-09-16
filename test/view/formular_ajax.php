<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

$petId = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pet Adoption Form - Pow</title>
    <link rel="stylesheet" href="../stiluri/formular.css" />
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="javascript:history.back()" class="back-button">
            <span class="back-arrow">←</span> Back
        </a>
        <a href="homepage.php" class="logo-link">
            <div class="logo">Pow</div>
        </a>
        <a href="profile.php" class="profile-icon">
            <img src="../stiluri/imagini/profileicon.png" alt="Profile" />
        </a>
    </header>

    <section class="formular-banner">
        <h1>PET ADOPTION FORM</h1>
    </section>

    <div id="form-messages" class="error-messages" style="display: none;"></div>
    <div id="success-message" class="success-message" style="display: none; margin-top: 20px; color: green; font-weight: bold; text-align: center;">
        Submitted with success! Thank you for your adoption request.
    </div>
    
    <div id="no-form-message" class="error-messages" style="display: none;">
        <p class="error">You have already submitted an adoption request for this pet.</p>
        <div class="return-link">
            <a href="pet-page.php?id=<?php echo htmlspecialchars($petId); ?>" class="back-btn">Return to pet page</a>
        </div>
    </div>

    <div id="form-container" style="display: none;">
        <section class="section-title">
            <h1>New Owner Information</h1>
        </section>

        <form id="adoption-form" class="adoption-form">
            <div class="form-group two-col">
                <label>First Name
                    <input type="text" name="first_name" required />
                </label>
                <label>Last Name
                    <input type="text" name="last_name" required />
                </label>
            </div>

            <div class="form-group two-col">
                <label>Email
                    <input type="email" name="email" placeholder="example@example.com" required />
                </label>
                <label>Phone Number
                    <input type="tel" name="phone" placeholder="(0000) 000 000" required />
                </label>
            </div>

            <div class="address-group">
                <label>Address
                    <input type="text" name="street_address" placeholder="Street Address" required />
                </label>
                <div class="city-country-row">
                    <label>City
                        <input type="text" name="city" required />
                    </label>
                    <label>Country
                        <input type="text" name="country" required />
                    </label>
                </div>
                <label>Postal/Zip Code
                    <input type="text" name="postal_code" required />
                </label>
            </div>

            <div class="form-group">
                <label>Name of Pet You Wish to Adopt
                    <input type="text" name="pet_name" required />
                </label>
            </div>

            <div class="form-group">
                <label class="question-label">Do you have a yard?</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="yard" value="yes" required />
                        <span class="radio-text">Yes</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="yard" value="no" />
                        <span class="radio-text">No</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="question-label">Do you own or rent your place?</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="housing" value="own" required />
                        <span class="radio-text">Own</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="housing" value="rent" />
                        <span class="radio-text">Rent</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="question-label">If renting, do you have the landlord's permission to have a pet?</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="landlord_permission" value="yes" />
                        <span class="radio-text">Yes</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="landlord_permission" value="no" />
                        <span class="radio-text">No</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="question-label">Are there any children in the home?</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="children" value="yes" required />
                        <span class="radio-text">Yes</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="children" value="no" />
                        <span class="radio-text">No</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>If yes, how many and how old?
                    <input type="text" name="children_details" />
                </label>
            </div>

            <div class="form-group">
                <label class="question-label">Do you own any pets?</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="current_pets" value="yes" required />
                        <span class="radio-text">Yes</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="current_pets" value="no" />
                        <span class="radio-text">No</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>If yes, what pets?
                    <input type="text" name="pet_details" />
                </label>
            </div>

            <div class="form-group">
                <label>How many hours per day would the pet be alone and why?
                    <textarea name="pet_alone_time" placeholder="Type here... (Please include the number of hours)" required></textarea>
                </label>
            </div>

            <div class="form-group">
                <label>Why do you want to adopt this pet?
                    <textarea name="adoption_reason" placeholder="Type here..." required></textarea>
                </label>
            </div>

            <section class="terms-section">
                <h2>Terms and Conditions</h2>
                <ul class="terms-list">
                    <li>By clicking the submit button, I agree to go adoption process will undergo thoroughly and check all above-mentioned information.</li>
                    <li>By clicking the submit button, I understand my references will be checked including veterinary and personal.</li>
                    <li>By clicking the submit button, I understand there is an adoption donation and this donation will support all of your local no kill rescue centers.</li>
                    <li>By clicking the submit button, I understand and agree that all pets from the Rescue Center are spayed or neutered prior to adoption.</li>
                    <li>By clicking the submit button, I understand there is no "cooling off" period, and that if the pet should not and will not leave Rescue Center until all above Rescue Center to make arrangements for my pet to be taken back into care.</li>
                    <li>By clicking the submit button, I agree to return the pet to the rescue center against any losses, forwards, clients, injury, damages incurred that may affect any persons as property by our adopted pet after custody transfer.</li>
                    <li>By clicking the submit button, I understand that Rescue Center will follow up on the pet's health or behaviour problems known by the above rescue center are disclosed at the time of adoption.</li>
                    <li>By clicking the submit button, I understand that if I no longer want my pet, or am no longer able to provide a good home that I will return my pet to the Rescue Center where I initially adopted my pet from. Rescue Center deems appropriate.</li>
                    <li>By clicking the submit button, I certify all of the above information is true and complete.</li>
                </ul>
            </section>

            <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($petId); ?>">
            <input type="hidden" name="action" value="submit">
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const petId = <?php echo $petId; ?>;
        
        // Check if user has already submitted a form for this pet
        fetch('../public/api.php?type=adoption&action=check_existing&pet_id=' + petId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.hasExistingForm) {
                        // Show message that user already submitted a form
                        document.getElementById('no-form-message').style.display = 'block';
                        document.getElementById('form-container').style.display = 'none';
                    } else {
                        // Show the form
                        document.getElementById('form-container').style.display = 'block';
                    }
                } else {
                    // Error checking form status
                    showErrorMessage('Error checking form status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                showErrorMessage('Error connecting to server. Please try again later.');
                console.error('Error:', error);
            });
        
        // Form submission
        const adoptionForm = document.getElementById('adoption-form');
        adoptionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(adoptionForm);
              // Clear previous error messages
            clearMessages();
            
            const submitBtn = adoptionForm.querySelector('.submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            fetch(`../public/api.php?type=adoption&action=submit&pet_id=${petId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message and hide form
                    document.getElementById('success-message').style.display = 'block';
                    document.getElementById('form-container').style.display = 'none';
                } else {
                    // Show error message
                    showErrorMessage(data.message || 'An error occurred while submitting the form');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('An error occurred while submitting the form. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit';
            });
                    document.getElementById('success-message').style.display = 'block';
                    document.getElementById('form-container').style.display = 'none';
                    
                    // Scroll to top of page
                    window.scrollTo(0, 0);
                } else {
                    // Show error messages
                    if (data.errors) {
                        showErrors(data.errors);
                    } else {
                        showErrorMessage(data.message || 'An error occurred while submitting the form');
                    }
                }
            })
            .catch(error => {
                showErrorMessage('Error connecting to server. Please try again later.');
                console.error('Error:', error);
            });
        });
        
        function showErrors(errors) {
            const messagesDiv = document.getElementById('form-messages');
            messagesDiv.innerHTML = '';
            
            errors.forEach(error => {
                const p = document.createElement('p');
                p.className = 'error';
                p.textContent = error;
                messagesDiv.appendChild(p);
            });
            
            messagesDiv.style.display = 'block';
            
            // Scroll to error messages
            messagesDiv.scrollIntoView({ behavior: 'smooth' });
        }
        
        function showErrorMessage(message) {
            const messagesDiv = document.getElementById('form-messages');
            messagesDiv.innerHTML = `<p class="error">${message}</p>`;
            messagesDiv.style.display = 'block';
            messagesDiv.scrollIntoView({ behavior: 'smooth' });
        }
        
        function clearMessages() {
            document.getElementById('form-messages').style.display = 'none';
            document.getElementById('form-messages').innerHTML = '';
            document.getElementById('success-message').style.display = 'none';
        }
    });
    </script>
</body>
</html>
