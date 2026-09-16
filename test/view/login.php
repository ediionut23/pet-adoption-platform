<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Pow</title>
    <link rel="stylesheet" href="../stiluri/styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
  </head>
  <body>
    <div class="login-container">
      <div class="login-box">
        <h1>LOGIN</h1>
        <div id="popup" class="popup"></div>
        <form id="loginForm" method="POST">
          <input type="hidden" name="action" value="login">
          <input type="text" name="name" placeholder="Name" class="input-field" required />
          <input type="text" name="surname" placeholder="Surname" class="input-field" required />
          <input type="password" name="password" placeholder="Password" class="input-field" required />
          <button type="submit" class="login-button" id="submitBtn">LOGIN</button>
        </form>
        <p class="signup-text">
          Don't have an account? <a href="register.php">Sign up</a>
        </p>
      </div>
      <div class="circle"></div>
      <div class="cat-image">
        <img src="../stiluri/imagini/cat-image.png" alt="Cat" />
      </div>
    </div>

    <script>
      function showPopup(message, type) {
        const popup = document.getElementById('popup');
        if (!popup) return;
        
        popup.textContent = typeof message === 'string' ? message : JSON.stringify(message);
        popup.className = 'popup ' + type + ' show';
        
        setTimeout(function() {
          popup.classList.remove('show');
        }, 3000);
      }

      async function handleSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = document.getElementById('submitBtn');
        const formData = new FormData(form);
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        try {
          const response = await fetch('../controllers/AuthController.php', {
            method: 'POST',
            body: formData
          });

          let data;
          const text = await response.text();
          try {
            data = JSON.parse(text);
          } catch (e) {
            console.error('Server response:', text);
            throw new Error('Invalid JSON response from server');
          }

          if (data.success) {
            window.location.href = data.redirect || 'homepage.php';
          } else {
            showPopup(data.message || 'Login failed', 'error');
          }
        } catch (error) {
          console.error('Error:', error);
          showPopup('Server error. Please try again later.', 'error');
        } finally {
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
        }
      }

      document.getElementById('loginForm').addEventListener('submit', handleSubmit);

      document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
          showPopup('Registration successful! Please login.', 'success');
          window.history.replaceState({}, document.title, window.location.pathname);
        }
      });
    </script>
  </body>
</html>