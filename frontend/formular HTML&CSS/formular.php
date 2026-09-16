<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pet Adoption Form - Pow</title>
    <link rel="stylesheet" href="formular.css" />
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
     <header class="navbar">
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">Pow</div>
        <a href="profile.html" class="profile-icon">
            <img src="images/profileicon.png" alt="Profile" />
        </a>
    </header>
    <section class="formular-banner">
        <h1>PET ADOPTION FORM</h1>
    </section>
    <section class="section-title">
        <h1>New Owner Information</h1>
    </section>
    <form class="adoption-form">
        <div class="form-group two-col">
             <label>First Name
                 <input type="text" name="first_name" />
             </label>
             <label>Last Name
                 <input type="text" name="last_name" />
             </label>
        </div>
        <div class="form-group two-col">
             <label>Email
                 <input type="email" name="email" placeholder="example@example.com" />
             </label>
             <label>Phone Number
                 <input type="tel" name="phone" placeholder="(0000) 000 000" />
             </label>
        </div>
        <div class="address-group">
            <label>Address
                <input type="text" name="street_address" placeholder="Street Address" />
            </label>
            <div class="city-country-row">
                <label>City
                    <input type="text" name="city" />
                </label>
                <label>Country
                    <input type="text" name="country" />
                </label>
            </div>
            <label>Postal/Zip Code
                <input type="text" name="postal_code" />
            </label>
        </div>

        <div class="form-group">
            <label>Name of Pet You Wish to Adopt
                <input type="text" name="pet_name" />
            </label>
        </div>

        <div class="form-group">
            <label class="question-label">Do you have a yard?</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="yard" value="yes" />
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
                    <input type="radio" name="housing" value="own" />
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
                    <input type="radio" name="children" value="yes" />
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
                    <input type="radio" name="current_pets" value="yes" />
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
                <textarea name="pet_alone_time" placeholder="Type here..."></textarea>
            </label>
        </div>

        <div class="form-group">
            <label>Why do you want to adopt this pet?
                <textarea name="adoption_reason" placeholder="Type here..."></textarea>
            </label>
        </div>
    </form>

    <section class="terms-section">
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

    <section class="signature-section">
    <div class="date-signature-container">
        <div class="date-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" />
        </div>
        

    </div>
    
    <button type="submit" class="submit-btn">Submit</button>
</section>


</body>
</html>