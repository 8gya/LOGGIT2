<?php
include '../php/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Debug database connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Bind results
    $stmt->bind_result($id, $hashed_password);

    if ($stmt->num_rows > 0 && $stmt->fetch()) {
        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Session regeneration for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Redirect to profile page
            header("Location: ../profile_template.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loggin</title>
    <link rel="stylesheet" href="../css/logginstyles.css">
</head>
<body>
    <div class="back-button-container">
        <a href="../index.php" class="back-button">BACK</a>
    </div>

    <h1>LOGGIN</h1>
    <h2>Welcome Back!</h2>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <button type="submit" class="custom-button">LOG IN</button>
    </form>
</body>
</html>
