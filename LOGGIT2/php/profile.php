<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loggin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, title, hours, rating, review FROM games WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$games = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (isset($_POST['delete_game'])) {
    $game_id = intval($_POST['game_id']);
    $stmt = $conn->prepare("DELETE FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $stmt->close();
    header("Location: profile.php");
    exit();
}

if (isset($_POST['update_game'])) {
    $game_id = intval($_POST['game_id']);
    $title = trim($_POST['title']);
    $hours = intval($_POST['hours']);
    $rating = floatval($_POST['rating']);
    $review = trim($_POST['review']);
    $stmt = $conn->prepare("UPDATE games SET title = ?, hours = ?, rating = ?, review = ? WHERE id = ?");
    $stmt->bind_param("sdisi", $title, $hours, $rating, $review, $game_id);
    $stmt->execute();
    $stmt->close();
    header("Location: profile.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profilestyles.css">
</head>
<body>
    <div class="back-button-container">
        <a href="logout.php" class="back-button">LOGOUT</a>
    </div>

    <h1>LOGGIT</h1>
    <div class="profile-container">
        <div class="user-details">
            <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
            <p><strong>Total Games Logged:</strong> <?php echo count($games); ?></p>
        </div>
        <div class="games-list">
            <h2>Logged Games</h2>
            <ul>
                <?php foreach ($games as $game): ?>
                    <li>
                        <strong><?php echo $game['title']; ?></strong> - 
                        <?php echo $game['hours']; ?> hours, 
                        Rating: <?php echo $game['rating']; ?>/10<br>
                        Review: <?php echo $game['review']; ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                            <button type="submit" name="delete_game" class="custom-button delete-button">Delete</button>
                        </form>
                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($game)); ?>)" class="custom-button">Edit</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="game_id" id="edit-game-id">
                <label for="edit-title">Game Title:</label>
                <input type="text" name="title" id="edit-title" required>
                <label for="edit-hours">Hours Played:</label>
                <input type="number" name="hours" id="edit-hours" required>
                <label for="edit-rating">Rating (1-10):</label>
                <input type="number" name="rating" id="edit-rating" required>
                <label for="edit-review">Review:</label>
                <textarea name="review" id="edit-review" rows="5" required></textarea>
                <button type="submit" name="update_game" class="custom-button">Save</button>
                <button type="button" class="custom-button" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script src="profile.js"></script>
</body>
</html>