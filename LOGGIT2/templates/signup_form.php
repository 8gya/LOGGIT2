<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../php/db.php'; // Ensure this file contains your database connection logic.
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("SQL Error: " . $conn->error); // Debug SQL error
        }

        // Bind parameters
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            // Log the user in by setting session variables
            session_start();
            $_SESSION['user_id'] = $conn->insert_id; // Get the user ID of the newly inserted user
            $_SESSION['username'] = $username;

            // Redirect to profile page after signup
            header("Location: ../profile_template.php");
            exit();
        } else {
            // Check for duplicate entry errors
            if ($stmt->errno === 1062) { // 1062 is the SQL error code for duplicate entry
                $error = "Username or email already exists.";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close database connection
    $conn->close();
}

// Display errors if any
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" href="../css/logginstyles.css">
</head>
<body>
    <div class="back-button-container">
        <a href="../index.php" class="back-button">BACK</a>
    </div>

    <h1>SIGNUP</h1>
    <h2>Create Your Account</h2>
    <div class="form-container">
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" name="confirm-password" required>

            <button type="submit" class="custom-button">SIGN UP</button>
        </form>
    </div>
</body>
</html>
