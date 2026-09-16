<?php
session_start();
require_once '../utils/auth_middleware.php';
$user = checkAuth();
require_once '../controllers/UserController.php';

$userId = $user->id;
$controller = new UserController();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->updateProfile($userId, $_POST);
    
    if (isset($result['success'])) {
        $message = $result['success'];
    } else {
        $message = $result['error'] ?? "An unknown error occurred";
    }
}

$userData = $controller->getUserProfile($userId);
if (isset($userData['error'])) {
    $message = $userData['error'];
} else {
    $user = $userData['user'];
    $adoptedPets = $userData['adoptedPets'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Pow</title>
    <link rel="stylesheet" href="../stiluri/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <a href="homepage.php" class="back-btn">Back</a>
        <a href="homepage.php" class="logo"><h1>Pow</h1></a>
        <button class="profile-button">
            <img src="../stiluri/imagini/profileicon.png" alt="Profile">
        </button>
    </header>

    <div class="title-bar">
        <h2>YOUR PROFILE</h2>
    </div>    <div class="profile-container">
        <img src="../stiluri/imagini/profileicon.png" alt="Profile Icon" class="profile-icon">
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">            <div class="form-group">
                <input type="text" name="name" placeholder="name" 
                       value="<?php echo htmlspecialchars($user['NAME'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <input type="text" name="surname" placeholder="surname" 
                       value="<?php echo htmlspecialchars($user['SURNAME'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="email adress" 
                       value="<?php echo htmlspecialchars($user['EMAIL'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <input type="password" name="current_password" placeholder="current password">
            </div>

            <div class="form-group">
                <input type="password" name="new_password" placeholder="new password">
            </div>

            <button type="submit" class="save-button">Save Changes</button>
        </form>

        <div class="profile-actions">
            <a href="my-pets.php" class="my-pets-btn">My Posted Pets</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>    <div class="adopted-pets">
        <h2>ADOPTED PETS</h2>
        <?php if (!empty($adoptedPets)): ?>
            <div class="pet-circles">
                <?php foreach ($adoptedPets as $pet): ?>
                    <div class="pet-circle">
                        <a href="pet-page.php?id=<?php echo htmlspecialchars($pet['ID']); ?>">
                            <div class="circle-container">
                                <img src="../<?php echo htmlspecialchars($pet['PET_IMAGE']); ?>" 
                                     alt="<?php echo htmlspecialchars($pet['NAME']); ?>"
                                     title="<?php echo htmlspecialchars($pet['NAME']); ?>">
                            </div>
                            <div class="pet-name"><?php echo htmlspecialchars($pet['NAME']); ?></div>
                            <div class="adoption-date">Adopted: <?php echo date('d M Y', strtotime($pet['ADOPTION_DATE'])); ?></div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No pets adopted yet.</p>
        <?php endif; ?>
    </div>    <style>
        .error {
            color: #d9534f;
            background-color: #f9f2f2;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            color: #5cb85c;
            background-color: #f2f9f2;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }        .profile-actions {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 20px;
        }        .my-pets-btn {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            text-align: center;
            width: 140px;
            display: inline-block;
        }        .logout-btn {
            padding: 8px 16px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            text-align: center;
            width: auto;
            min-width: 140px;
            display: inline-block;
        }
        .my-pets-btn {
            background-color: #4CAF50;
            color: white;
            border: 2px solid #4CAF50;
        }        .my-pets-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
        .save-button {
            padding: 10px 20px;
            border-radius: 25px;
            background-color: #3498db;
            color: white;
            border: 2px solid #3498db;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        .save-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .logout-btn {
            background-color: #ff4444;
            color: white;
            border: 2px solid #ff4444;
        }
        .logout-btn:hover {
            background-color: #cc0000;
            transform: translateY(-2px);
        }
    </style>
</body>
</html>
</body>
</html>