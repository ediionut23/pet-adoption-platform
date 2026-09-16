<?php
session_start();
require_once '../utils/auth_middleware.php';
$user = checkAuth();

$_SESSION['user_id'] = $user->id;
$_SESSION['is_admin'] = $user->is_admin ?? false;
?>
<!DOCTYPE html>
<html lang = "en">
    <head>
         <meta charset="UTF-8" />
         <meta name="viewport" content="width=device-width, initial-scale=1.0" />
         <title>Pow - Pet Adoption</title>
         <link rel="stylesheet" href="../stiluri/homepage.css" />
          <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    </head>

    <body>
        <header class="navbar">
            <div class="nav-left">
                <a href="post-pet.php" class="add-pet-btn">
                    <span class="plus-icon">+</span>
                    <span>Add</span>
                </a>
                <a href="adoption-requests.php" class="requests-btn">
                    <span>Requests</span>
                </a>
                <?php if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']): ?>
                <a href="adoption-status.php" class="requests-btn">
                    <span>My Adoptions</span>
                </a>
                <?php endif; ?>
            </div>

            <div class="logo">Pow</div>

            <div class="nav-right">
                <a href="messages.php" class="messages-link">Messages</a>
                <a href="news.php" class="messages-link">News</a>
                <?php if (isset($_SESSION['user_id'])): ?>                <a href="notifications.php" class="messages-link">
                    Notify
                    <span id="notification-count" class="notify-count" style="display: none;"></span>
                </a>
                <?php endif; ?>
                <a href="profile.php" class="profile-icon">
                    <img src="../stiluri/imagini/profileicon.png" alt="Profile" />
                </a>
            </div>
        </header>

        <section class = "welcome">
            <h1 class = "wel">Welcome to Pow! </h1>
            <h2 class = "choose">Choose your pet</h2>

            <div class="pet-options">
                <div class="pet-card">
                    <a href="lista-animale.php?type=cat">
                        <img src="../stiluri/imagini/cat.png" alt="cat">
                    </a>
                    <span>cat</span>
                </div>
                <div class="pet-card">
                    <a href="lista-animale.php?type=dog">
                        <img src="../stiluri/imagini/dog.png" alt="dog">
                    </a>
                     <span>dog</span>
                </div>
            <div class="pet-card">
                <a href="lista-animale.php?type=bird">
                    <img src="../stiluri/imagini/bird.png" alt="bird">
                </a>
                <span>bird</span>
            </div>
            <div class="pet-card">
                <a href="lista-animale.php?type=fish">
                    <img src="../stiluri/imagini/fish.png" alt="fish">
                </a>
                <span>fish</span>
            </div>
            <div class="pet-card">
                <a href="lista-animale.php?type=reptile">
                    <img src="../stiluri/imagini/reptilian.png" alt="reptilian">
                </a>
                <span>reptilian</span>
            </div>
        </div>
        </section>

        <section class = "why-adopt">
             <h2>Why Adopt a Pet?</h2>
             <div class="why-content">
                <ul>
                    <li><span>Save a life</span> - Give a homeless animal a second chance and a loving home.</li>
                    <li><span>Reduce overpopulation</span> - Help shelters free up space for other animals in need.</li>
                    <li><span>More affordable</span> -Adoption is often cheaper than buying from breeders or pet stores.</li>
                    <li><span>Emotionally rewarding</span> - Pets offer unconditional love, companionship, and emotional support.</li>
                    <li><span>Make a difference</span> - Be part of a compassionate, responsible community of pet lovers.</li>
                </ul>
                <img src="../stiluri/imagini/dog-woman.png" alt="woman with dog" />
             </div>
        </section>

         <div class="comics">
            <div class="meme1"><img src="../stiluri/imagini/meme1.png" alt=""></div>
            <div class="meme"><img src="../stiluri/imagini/meme2.png" alt=""></div>
            <div class="meme"><img src="../stiluri/imagini/meme3.png" alt=""></div>
         </div>

        <section class="oui">
            <div class="split-section">
                <div class="left-side">
                    <img src="../stiluri/imagini/happydog.png" class="dog-image" alt="happy dog" />
                </div>
                <div class="right-side">
                    <h3>Get a Pet!</h3>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                fetchUnreadNotifications();
                
                setInterval(fetchUnreadNotifications, 60000);
                
                function fetchUnreadNotifications() {
                    fetch('../public/api.php?type=notifications&action=get_unread_count')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.count > 0) {
                                const countElement = document.getElementById('notification-count');
                                countElement.textContent = data.count;
                                countElement.style.display = 'inline-block';
                            } else {
                                document.getElementById('notification-count').style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching notifications:', error);
                        });
                }
            });
        </script>
    </body>
</html>