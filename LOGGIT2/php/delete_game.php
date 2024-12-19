<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: loggin.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $game_id = $_POST['game_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM games WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $game_id, $user_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: ../profile.php");
    exit();
}
?>