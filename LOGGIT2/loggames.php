<?php
session_start();

include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location:templates/loggin_form.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['game-title']);
    $hours = intval($_POST['hours-played']);
    $rating = floatval($_POST['game-rating']);
    $review = trim($_POST['game-review']);

    $stmt = $conn->prepare("INSERT INTO games (user_id, title, hours, rating, review) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdis", $user_id, $title, $hours, $rating, $review);

    if ($stmt->execute()) {
        header("Location: profile_template.php");
        exit();
    } else {
        $error = "Error logging the game. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log New Game</title>
    <link rel="stylesheet" href="css/logstyles.css">
</head>
<body>
    <!-- Back Button -->
    <a href="profile_template.php" class="back-button">BACK</a>

    <!-- Page Title -->
    <h1>LOG NEW GAME</h1>

    <!-- Game Logging Form -->
    <div class="form-container">
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST">
            <label for="game-title">Game Title:</label>
            <input type="text" name="game-title" id="game-title" placeholder="Enter game title" required>

            <label for="hours-played">Hours Played:</label>
            <input type="number" name="hours-played" id="hours-played" placeholder="Enter hours played" min="0" required>

            <label for="game-rating">Rating (1-10):</label>
            <input type="number" name="game-rating" id="game-rating" placeholder="Enter rating" min="1" max="10" required>

            <label for="game-review">Review:</label>
            <textarea name="game-review" id="game-review" rows="5" placeholder="Write your review here..." required></textarea>

            <button type="submit" class="custom-button">LOG GAME</button>
        </form>
    </div>
</body>
</html>