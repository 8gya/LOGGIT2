<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loggit Index Page</title>
    <link rel="stylesheet" href="css/indexstyles.css">
</head>
<body>
    <div class="admin-button-container">
       
       
        <a href="templates/admin_login.php" class="custom-button">ADMIN LOGIN</a>
       
    </div>

    <h1>LOGGIT</h1>
    <h2>Log the games you've played</h2>
    <h2>Write reviews</h2>
    <h2>Track Hours Played</h2>

    <div class="button-container">
        <!-- Show Login and Sign Up buttons only if the user is not logged in -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="templates/loggin_form.php" class="custom-button">LOGGIN</a>
            <a href="templates/signup_form.php" class="custom-button">SIGN UP</a>
        <?php else: ?>
            <!-- Show Log Out button if the user is logged in -->
            <a href="php/logout.php?logout=true" class="custom-button">LOGOUT</a>
        <?php endif; ?>
    </div>
</body>
</html>
