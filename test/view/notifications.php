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
    <title>Notifications - Pow</title>
    <link rel="stylesheet" href="../stiluri/styles.css">
    <link rel="stylesheet" href="../stiluri/notifications.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="nav-left">
            <a href="homepage.php" class="back-btn">
                ← Back
            </a>
        </div>
        <div class="logo">Pow</div>
        <div class="nav-right">
            <a href="profile.php" class="profile-icon">
                <img src="../stiluri/imagini/profileicon.png" alt="Profile">
            </a>
        </div>
    </header>    <div class="notifications-container">
        <h1>Notifications</h1>
        
        <div class="loading-spinner" id="loading-spinner"></div>
        
        <div id="notifications-header" style="display: none; text-align: right;">
            <button id="mark-all-read-btn" class="mark-all-read">
                Mark all as read
            </button>
        </div>
        
        <div id="notifications-list">
            <!-- Notifications will be loaded here via JavaScript -->
        </div>
    </div>    <style>
        .notification-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .message-owner-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .message-owner-btn:hover {
            background-color: #45a049;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchNotifications();
            
            document.getElementById('mark-all-read-btn').addEventListener('click', markAllAsRead);
        });
        
        function showLoadingSpinner(show = true) {
            const spinner = document.getElementById('loading-spinner');
            spinner.style.display = show ? 'flex' : 'none';
        }
        
        function showMessage(message, isError = false) {
            let messageElement = document.getElementById('notification-message');
            if (!messageElement) {
                messageElement = document.createElement('div');
                messageElement.id = 'notification-message';
                messageElement.className = 'notification-message';
                document.querySelector('.notifications-container').prepend(messageElement);
            }
            
            messageElement.textContent = message;
            messageElement.className = 'notification-message ' + (isError ? 'error' : 'success');
            messageElement.style.display = 'block';
            
            setTimeout(() => {
                messageElement.style.display = 'none';
            }, 5000);
        }
        
        async function fetchNotifications() {
            showLoadingSpinner(true);
            document.getElementById('notifications-list').innerHTML = '';
            document.getElementById('notifications-header').style.display = 'none';
            
            try {                const response = await fetch('../public/api.php?type=notifications&action=get_notifications');
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const data = await response.json();
                  console.log('API response:', data); // Adăugăm logging pentru debugging
                
                if (data.success && data.notifications) {
                    displayNotifications(data.notifications);
                    document.getElementById('notifications-header').style.display = 'block';
                } else {
                    showMessage(data.message || 'Failed to load notifications', true);
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                showMessage('An error occurred while loading notifications', true);
            } finally {
                showLoadingSpinner(false);
            }
        }
        
        function displayNotifications(notifications) {
            const container = document.getElementById('notifications-list');
            
            if (notifications.length === 0) {
                container.innerHTML = '<p class="no-notifications">You have no notifications</p>';
                document.getElementById('mark-all-read-btn').style.display = 'none';
                return;
            }
            
            document.getElementById('mark-all-read-btn').style.display = 'block';                const notificationsHTML = notifications.map(notification => {
                const isRead = notification.IS_READ == 1;
                const notificationClass = isRead ? 'notification read' : 'notification unread';
                const petLink = notification.PET_NAME ? 
                    `<a href="pet-page.php?id=${notification.RELATED_PET_ID}">${notification.PET_NAME}</a>` : '';
                
                let content = notification.MESSAGE;
                if (notification.PET_NAME) {
                    content = content.replace(notification.PET_NAME, petLink);
                }
                
                const isAdoptionApproved = notification.MESSAGE && notification.MESSAGE.toLowerCase().includes('approved') && 
                                          notification.MESSAGE.toLowerCase().includes('adoption') && 
                                          notification.PET_NAME && notification.RELATED_PET_ID;
                
                const ownerId = notification.OWNER_ID || null;
                
                return `
                    <div class="${notificationClass}" data-id="${notification.ID}">
                        <div class="notification-content">${content}</div>
                        <div class="notification-time">
                            ${new Date(notification.CREATED_AT).toLocaleDateString('en-GB', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </div>
                        <div class="notification-actions">
                            ${!isRead ? 
                                `<button class="mark-read-btn" onclick="markAsRead(${notification.ID})">
                                    Mark as read
                                </button>` : 
                                ''}
                            ${isAdoptionApproved ? 
                                `<button class="message-owner-btn" onclick="messageOwner(${notification.RELATED_PET_ID})">
                                    Message owner to arrange pickup
                                </button>` : 
                                ''}
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = notificationsHTML;
        }
        
        async function markAsRead(notificationId) {
            try {                const response = await fetch('../public/api.php?type=notifications&action=mark_read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ notification_id: notificationId })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    const notification = document.querySelector(`.notification[data-id="${notificationId}"]`);
                    if (notification) {
                        notification.classList.remove('unread');
                        notification.classList.add('read');
                        const button = notification.querySelector('.mark-read-btn');
                        if (button) {
                            button.remove();
                        }
                    }
                    showMessage('Notification marked as read');
                } else {
                    showMessage(data.message || 'Failed to mark notification as read', true);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
                showMessage('An error occurred', true);
            }
        }
        async function markAllAsRead() {
            showLoadingSpinner(true);
            
            try {                const response = await fetch('../public/api.php?type=notifications&action=mark_all_read', {
                    method: 'POST'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    fetchNotifications();
                    showMessage('All notifications marked as read');
                } else {
                    showMessage(data.message || 'Failed to mark all notifications as read', true);
                    showLoadingSpinner(false);
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
                showMessage('An error occurred', true);
                showLoadingSpinner(false);
            }
        }
        async function messageOwner(petId) {
            try {
                showLoadingSpinner(true);
                
                const response = await fetch(`../public/api.php?type=pets&action=get_details&id=${petId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.pet) {
                    const ownerId = data.pet.OWNER_ID || data.pet.owner_id;
                    
                    if (ownerId) {
                        window.location.href = `messages.php?pet_id=${petId}&owner_id=${ownerId}`;
                    } else {
                        showMessage('Could not retrieve owner information', true);
                        showLoadingSpinner(false);
                    }
                } else {
                    showMessage('Could not retrieve pet information', true);
                    showLoadingSpinner(false);
                }
            } catch (error) {
                console.error('Error getting pet owner details:', error);
                showMessage('An error occurred while trying to message the owner', true);
                showLoadingSpinner(false);
            }
        }
    </script>
</body>
</html>