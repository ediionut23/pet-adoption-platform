<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

session_start();
require_once '../controllers/AdoptionRequestController.php';

$controller = new AdoptionRequestController();
$result = $controller->index();

if (isset($result['error'])) {
    echo "Error: " . $result['error'];
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Requests - Pow</title>
    <link rel="stylesheet" href="../stiluri/adoption-requests.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <div class="overlay"></div>

    <header class="navbar">
        <a href="homepage.php" class="back-button">
            <span class="back-icon">←</span>
            <span>Back</span>
        </a>
        <a href="homepage.php" class="logo">Pow</a>
        <a href="profile.php" class="profile-icon">
            <img src="../stiluri/imagini/profileicon.png" alt="Profile">
        </a>
    </header>

    <div class="title-banner">
        <h1>ADOPTION REQUESTS</h1>
    </div>

    <div class="requests-container">
        <?php 
        $has_requests = false;
        foreach ($result as $row) { 
            $has_requests = true;
            $image_path = $controller->getImagePath($row['PET_IMAGE'], $row['SPECIES']);
        ?>
            <div class="request-card">
                <div class="pet-info">
                    <img src="../<?php echo htmlspecialchars($image_path); ?>" 
                         alt="<?php echo htmlspecialchars($row['PET_NAME']); ?>" 
                         class="pet-image">
                    <div class="pet-details">
                        <h2><?php echo htmlspecialchars($row['PET_NAME']); ?></h2>
                        <p>Adoption request<br>coming from <?php echo htmlspecialchars($row['FIRST_NAME'] . ' ' . $row['LAST_NAME']); ?></p>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="see-form-btn" onclick="window.location.href='view-form.php?id=<?php echo $row['FORM_ID']; ?>'">
                        See form
                    </button>
                    <button class="accept-btn" onclick="updateStatus(<?php echo $row['FORM_ID']; ?>, 'approved')">
                        Accept
                    </button>
                    <button class="decline-btn" onclick="updateStatus(<?php echo $row['FORM_ID']; ?>, 'rejected')">
                        Decline
                    </button>
                </div>
            </div>
        <?php } 
        
        if (!$has_requests) {
            echo '<div class="no-requests">
                    <p>You don\'t have any adoption requests at the moment.</p>
                  </div>';
        }
        ?>
    </div>

    <script>
    function updateStatus(formId, status) {
        if (confirm('Are you sure you want to ' + status + ' this adoption request?')) {
            fetch('../controllers/AdoptionFormController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'form_id=' + formId + '&status=' + status
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.querySelector('.hamburger');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');
        const closeButton = document.querySelector('.close-sidebar');

        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        hamburger.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);
        closeButton.addEventListener('click', closeSidebar);
    });
    </script>
</body>
</html> 