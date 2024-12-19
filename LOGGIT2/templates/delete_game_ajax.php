<?php
session_start();

if (isset($_POST['game_id'])) {
    $game_id = $_POST['game_id'];

    // Include the database connection
    include '../php/db.php';

    // Delete the game from the database
    $query = $conn->prepare("DELETE FROM games WHERE id = ?");
    $query->bind_param("i", $game_id);

    if ($query->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $query->close();
    $conn->close();
} else {
    echo "Missing game ID";
}
?>
