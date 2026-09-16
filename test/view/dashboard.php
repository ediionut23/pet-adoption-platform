<?php
    session_start();
    if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_surname'])) {
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pow</title>
    <link rel="stylesheet" href="../stiluri/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Welcome</h1>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['user_name'] . " " . $_SESSION['user_surname']); ?>!</p>
            <form action="logout.php" method="POST">
                <button type="submit" class="login-button">Logout</button>
            </form>
        </div>
        <div class="circle"></div>
    </div>
</body>
</html> 