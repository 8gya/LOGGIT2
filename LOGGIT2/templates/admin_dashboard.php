<?php
session_start();
include '../php/db.php';

// Redirect if not admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Fetch all users
$stmt_users = $conn->prepare("SELECT id, username, email FROM users");
$stmt_users->execute();
$result_users = $stmt_users->get_result();
$users = $result_users->fetch_all(MYSQLI_ASSOC);
$stmt_users->close();

// Fetch all games
$stmt_games = $conn->prepare("SELECT games.id AS game_id, games.title, games.hours, games.rating, games.review, users.username AS user 
                              FROM games INNER JOIN users ON games.user_id = users.id");
$stmt_games->execute();
$result_games = $stmt_games->get_result();
$games = $result_games->fetch_all(MYSQLI_ASSOC);
$stmt_games->close();

// Handle delete user
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt_games = $conn->prepare("DELETE FROM games WHERE user_id = ?");
    $stmt_games->bind_param("i", $user_id);
    $stmt_games->execute();

    $stmt->close();
    $stmt_games->close();
    header("Location: admin_dashboard.php");
    exit();
}

// Handle delete game
if (isset($_POST['delete_game'])) {
    $game_id = intval($_POST['game_id']);
    $stmt = $conn->prepare("DELETE FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/adminstyles.css">
</head>
<body>
    <div class="back-button-container">
        <a href="../index.php" class="back-button">BACK</a>
        <a href="../php/admin-logout.php?logout=true" class="back-button">LOGOUT</a>
    </div>
    <h1>ADMIN DASHBOARD</h1>

    <div class="admin-section">
        <h2>Users</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <strong><?php echo $user['username']; ?></strong> (<?php echo $user['email']; ?>)
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="delete_user" class="custom-button delete-button">Delete User</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="admin-section">
        <h2>Logged Games</h2>
        <ul>
            <?php foreach ($games as $game): ?>
                <li>
                    <strong><?php echo $game['title']; ?></strong> by <?php echo $game['user']; ?> - 
                    <?php echo $game['hours']; ?> hours, Rating: <?php echo $game['rating']; ?>/10<br>
                    Review: <?php echo $game['review']; ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="game_id" value="<?php echo $game['game_id']; ?>">
                        <button type="submit" name="delete_game" class="custom-button delete-button">Delete Game</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>