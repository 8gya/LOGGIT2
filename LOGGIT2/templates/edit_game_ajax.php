<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (isset($_POST['game_id']) && isset($_POST['rating']) && isset($_POST['review']) && isset($_POST['additional_hours'])) {
    $game_id = $_POST['game_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $additional_hours = $_POST['additional_hours'];

    // Include the database connection
    include '../php/db.php';

    // Fetch the current hours played for this game
    $query = $conn->prepare("SELECT hours FROM games WHERE id = ?");
    $query->bind_param("i", $game_id);
    $query->execute();
    $result = $query->get_result();
    $current_game = $result->fetch_assoc();

    if ($current_game) {
        // Calculate the new total hours
        $new_total_hours = $current_game['hours'] + $additional_hours;

        // Update the game record with new hours, rating, and review
        $update_query = $conn->prepare("UPDATE games SET rating = ?, review = ?, hours = ? WHERE id = ?");
        $update_query->bind_param("dsii", $rating, $review, $new_total_hours, $game_id);

        if ($update_query->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $query->close();
        $update_query->close();
    } else {
        echo "Game not found";
    }

    $conn->close();
} else {
    echo "Missing data";
}
?>
