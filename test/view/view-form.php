<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adoption-requests.php");
    exit;
}

try {
    $conn = getConnection();
    
    $query = "SELECT 
        af.*,
        p.name as pet_name,
        p.owner_id as pet_owner_id
    FROM adoption_form af
    JOIN pets p ON af.pet_id = p.id
    WHERE af.id = :form_id";
    
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":form_id", $_GET['id']);
    oci_execute($stmt);
    
    $form = oci_fetch_assoc($stmt);
    
    if (!$form || ($form['PET_OWNER_ID'] != $_SESSION['user_id'] && $form['USER_ID'] != $_SESSION['user_id'])) {
        header("Location: adoption-requests.php");
        exit;
    }
    
    $is_pet_owner = ($form['PET_OWNER_ID'] == $_SESSION['user_id']);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Adoption Form - Pow</title>
    <link rel="stylesheet" href="../stiluri/formular.css" />
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
        <div class="nav-right">            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <a href="post-pet.php" class="add-pet-btn">
                <span class="plus-icon">+</span>
                <span>Add</span>
            </a>
            <a href="adoption-requests.php" class="requests-btn">
                <span>Requests</span>
            </a>
            <?php else: ?>
            
            <?php endif; ?>
            <a href="profile.php" class="profile-icon">
                <img src="../stiluri/imagini/profileicon.png" alt="Profile">
            </a>
        </div>
    </header>
    
    <section class="formular-banner">
        <h1>ADOPTION FORM DETAILS</h1>
    </section>
    
    <section class="section-title">
        <h1>Potential Adopter Information</h1>
        <p>Status: 
            <span class="status-badge status-<?php echo strtolower($form['STATUS']); ?>">
                <?php echo htmlspecialchars($form['STATUS']); ?>
            </span>
        </p>
    </section>
    
    <div class="adoption-form">
        <div class="form-group two-col">
            <div>
                <label>First Name</label>
                <div class="form-value"><?php echo htmlspecialchars($form['FIRST_NAME'] ?? ''); ?></div>
            </div>
            <div>
                <label>Last Name</label>
                <div class="form-value"><?php echo htmlspecialchars($form['LAST_NAME'] ?? ''); ?></div>
            </div>
        </div>
        
        <div class="form-group two-col">
            <div>
                <label>Email</label>
                <div class="form-value"><?php echo htmlspecialchars($form['EMAIL'] ?? ''); ?></div>
            </div>
            <div>
                <label>Phone Number</label>
                <div class="form-value"><?php echo htmlspecialchars($form['PHONE'] ?? ''); ?></div>
            </div>
        </div>
        
        <div class="address-group">
            <div>
                <label>Address</label>
                <div class="form-value"><?php echo htmlspecialchars($form['STREET_ADDRESS'] ?? ''); ?></div>
            </div>
            <div class="city-country-row">
                <div>
                    <label>City</label>
                    <div class="form-value"><?php echo htmlspecialchars($form['CITY'] ?? ''); ?></div>
                </div>
                <div>
                    <label>Country</label>
                    <div class="form-value"><?php echo htmlspecialchars($form['COUNTRY'] ?? ''); ?></div>
                </div>
            </div>
            <div>
                <label>Postal/Zip Code</label>
                <div class="form-value"><?php echo htmlspecialchars($form['POSTAL_CODE'] ?? ''); ?></div>
            </div>
        </div>

        <div class="form-group">
            <label>Name of Pet They Wish to Adopt</label>
            <div class="form-value"><?php echo htmlspecialchars($form['PET_NAME_DESIRED'] ?? ''); ?></div>
        </div>

        <div class="form-group">
            <label>Do they have a yard?</label>
            <div class="form-value"><?php echo $form['HAS_YARD'] ? 'Yes' : 'No'; ?></div>
        </div>

        <div class="form-group">
            <label>Living Situation</label>
            <div class="form-value"><?php echo htmlspecialchars($form['HOUSING_STATUS'] ?? ''); ?></div>
        </div>

        <div class="form-group">
            <label>Experience with Pets</label>
            <div class="form-value"><?php echo htmlspecialchars($form['OTHER_PETS_DESCRIPTION'] ?? ''); ?></div>
        </div>

        <div class="form-group">
            <label>Additional Comments</label>
            <div class="form-value"><?php echo htmlspecialchars($form['ADOPTION_REASON'] ?? ''); ?></div>
        </div>

        <div class="form-actions">
            <?php if ($is_pet_owner): ?>
            <button type="button" class="accept-btn" onclick="updateStatus(<?php echo $form['ID']; ?>, 'approved')">
                Accept
            </button>
            <button type="button" class="decline-btn" onclick="updateStatus(<?php echo $form['ID']; ?>, 'rejected')">
                Decline
            </button>
            <?php endif; ?>
            <button type="button" class="back-btn" onclick="window.location.href='<?php echo $is_pet_owner ? 'adoption-requests.php' : 'adoption-status.php'; ?>'">
                Back
            </button>
        </div>
    </div>

    <?php if ($is_pet_owner): ?>
    <script>
        function updateStatus(formId, status) {
            fetch('../controllers/AdoptionFormController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `form_id=${formId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'adoption-requests.php';
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status.');
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>

<?php
try {
} finally {
    if (isset($stmt)) {
        oci_free_statement($stmt);
    }
    if (isset($conn)) {
        oci_close($conn);
    }
}
?> 