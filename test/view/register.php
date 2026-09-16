<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: homepage.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Pow</title>
    <link rel="stylesheet" href="../stiluri/styles.css" />
    <link rel="stylesheet" href="../stiluri/register.css" />
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
  </head>
  <body>
    <div class="login-container">
      <div class="login-box">
        <h1>REGISTER</h1>
        <div id="popup" class="popup"></div>
        <form id="registerForm" method="POST">
          <input type="hidden" name="action" value="register">
          <input type="text" name="name" placeholder="Name" class="input-field" required />
          <input type="text" name="surname" placeholder="Surname" class="input-field" required />
          <input type="email" name="email" placeholder="Email" class="input-field" required />
          <input type="password" name="password" placeholder="Password" class="input-field" required />
          <input type="password" name="confirm_password" placeholder="Confirm Password" class="input-field" required />
          <button type="submit" class="login-button" id="submitBtn">REGISTER</button>
        </form>
        <p class="signup-text">
          Already have an account? <a href="login.php">Login</a>
        </p>
      </div>
      <div class="circle"></div>
      <div class="cat-image">
        <img src="../stiluri/imagini/cat-image.png" alt="Cat" />
      </div>
    </div>

    <script>
      function showPopup(message, type, isErrorList) {
        var popup = document.getElementById('popup');
        
        if (isErrorList && Array.isArray(message)) {
          var ul = document.createElement('ul');
          ul.className = 'error-list';
          message.forEach(function(error) {
            var li = document.createElement('li');
            li.textContent = error;
            ul.appendChild(li);
          });
          popup.innerHTML = '';
          popup.appendChild(ul);
        } else {
          popup.textContent = typeof message === 'string' ? message : JSON.stringify(message);
        }
        
        popup.className = 'popup ' + type + ' show';
        
        setTimeout(function() {
          popup.classList.remove('show');
        }, 3000);
      }

      function handleSubmit(event) {
        event.preventDefault();
        
        var form = event.target;
        var submitBtn = document.getElementById('submitBtn');
        var formData = new FormData(form);
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;        fetch('../public/api.php?type=user&action=register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.text().then(text => {
            try {
              return JSON.parse(text);
            } catch (e) {
              console.error('Server response:', text);
              throw new Error('Invalid JSON response from server');
            }
          });
        })
        .then(data => {
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
          
          if (data.success) {
            window.location.href = 'login.php?success=1';
          } else {
            showPopup(data.errors || ['Registration failed'], 'error', true);
          }
        })
        .catch(error => {
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
          console.error('Error:', error);
          showPopup(['Server error. Please try again later.'], 'error', true);
        });
      }

      document.getElementById('registerForm').addEventListener('submit', handleSubmit);

      document.querySelector('input[name="email"]').addEventListener('input', function(e) {
        var email = e.target.value;          var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
          e.target.setCustomValidity('Please enter a valid email address');
        } else {
          e.target.setCustomValidity('');
        }
      });

      document.querySelector('input[name="confirm_password"]').addEventListener('input', function(e) {
        var password = document.querySelector('input[name="password"]').value;
        var confirmPassword = e.target.value;
        
        if (password !== confirmPassword) {
          e.target.setCustomValidity('Passwords do not match');
        } else {
          e.target.setCustomValidity('');
        }
      });
    </script>
  </body>
</html>