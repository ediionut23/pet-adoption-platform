<?php
session_start();
require_once '../controllers/AdoptionStatusController.php';

$controller = new AdoptionStatusController();
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
    <title>My Adoption Status - Pow</title>
    <link rel="stylesheet" href="../stiluri/adoption-requests.css">
    <link rel="stylesheet" href="../stiluri/adoption-status.css">
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
        <div class="nav-right">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <a href="post-pet.php" class="add-pet-btn">
                <span class="plus-icon">+</span>
                <span>Add</span>
            </a>
            <a href="adoption-requests.php" class="requests-btn">
                <span>Requests</span>
            </a>
            <?php endif; ?>
            <a href="profile.php" class="profile-icon">
                <img src="../stiluri/imagini/profileicon.png" alt="Profile">
            </a>
        </div>
    </header>

    <div class="title-banner">
        <h1>MY ADOPTION REQUESTS</h1>
    </div>

    <div class="requests-container">
        <?php 
        $has_requests = false;
        foreach ($result as $row) { 
            $has_requests = true;
            $statusClass = $controller->getStatusClass($row['STATUS']);
            $formattedDate = $controller->formatDate($row['FORM_SUBMITTED_DATE']);
            $image_path = $controller->getImagePath($row['PET_IMAGE'], $row['SPECIES']);
        ?>
            <div class="request-card">
                <div class="pet-info">
                    <img src="../<?php echo htmlspecialchars($image_path); ?>" 
                         alt="<?php echo htmlspecialchars($row['PET_NAME']); ?>" 
                         class="pet-image">
                    <div class="pet-details">
                        <h2><?php echo htmlspecialchars($row['PET_NAME']); ?></h2>
                        <p>
                            Status: <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['STATUS']); ?>
                            </span>
                        </p>
                        <p class="request-date">Submitted on <?php echo $formattedDate; ?></p>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="see-form-btn" onclick="window.location.href='view-form.php?id=<?php echo $row['FORM_ID']; ?>'">
                        View Details
                    </button>
                </div>
            </div>
        <?php } 
        
        if (!$has_requests) {
            echo '<div class="no-requests">
                    <p>You haven\'t submitted any adoption requests yet.</p>
                  </div>';
        }
        ?>
    </div>

    <script>
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
        if (closeButton) {
            closeButton.addEventListener('click', closeSidebar);
        }
    });
    </script>
</body>
</html> 