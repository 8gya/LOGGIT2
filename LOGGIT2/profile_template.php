<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'php/db.php';
$user_id = $_SESSION['user_id'];

// Fetch user games
$query = $conn->prepare("SELECT * FROM games WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$games = [];
$total_hours = 0;
while ($row = $result->fetch_assoc()) {
    $games[] = $row;
    $total_hours += $row['hours'];
}

$query->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profilestyles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: black;
            border: 2px solid white;
            border-radius: 10px;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            z-index: 1000;
        }

        .modal-content {
            color: white;
            font-family: 'Press Start 2P', cursive;
            text-align: left;
        }

        .modal textarea, .modal input {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 2px solid white;
            background-color: black;
            color: white;
            font-family: 'Press Start 2P', cursive;
        }

    </style>
</head>
<body>
    <div class="back-button-container">
        <a href="php/logout.php?logout=true" class="back-button">LOGOUT</a>
    </div>

    <h1>LOGGIT</h1>
    <div class="profile-container">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <p><strong>Total Hours Played:</strong> <?php echo $total_hours; ?></p>
        <div class="games-list">
            <h2>Your Logged Games</h2>
            <ul>
                <?php foreach ($games as $game): ?>
                    <li class="game-item">
                        <strong><?php echo htmlspecialchars($game['title']); ?></strong> - <?php echo $game['hours']; ?> Hours<br>
                        <strong>Rating:</strong> <?php echo $game['rating'] ?? 'N/A'; ?>/10<br>
                        <strong>Review:</strong> <?php echo htmlspecialchars($game['review'] ?? 'No review yet.'); ?><br>
                        <button 
                            type="button" 
                            class="custom-button edit-button" 
                            data-id="<?php echo $game['id']; ?>" 
                            data-title="<?php echo htmlspecialchars($game['title']); ?>" 
                            data-rating="<?php echo $game['rating'] ?? ''; ?>" 
                            data-review="<?php echo htmlspecialchars($game['review'] ?? ''); ?>"
                            data-currenthours="<?php echo $game['hours']; ?>"> <!-- Added current hours data -->
                            Edit Review & Rating
                        </button>
                        <!-- Delete Button -->
                        <button 
                            type="button" 
                            class="custom-button delete-button" 
                            data-id="<?php echo $game['id']; ?>">Delete Game</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <a href="loggames.php" class="custom-button">LOG NEW GAME</a>

    <!-- Modal Structure -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Edit Review & Rating</h2>
            <form id="editForm">
                <input type="hidden" id="gameId" name="game_id">
                <label for="modalRating">Rating (1-10):</label><br>
                <input type="number" id="modalRating" name="rating" min="1" max="10" step="0.1" required><br><br>
                <label for="modalReview">Review:</label><br>
                <textarea id="modalReview" name="review" rows="5" required></textarea><br><br>
                <label for="modalHours">Additional Hours:</label><br>
                <input type="number" id="modalHours" name="additional_hours" min="0" step="0.1" required><br><br>
                <button type="button" id="saveChanges" class="custom-button">Save Changes</button>
                <button type="button" id="closeModal" class="custom-button">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Open the modal and populate it with game data
            $('.edit-button').on('click', function () {
                const gameId = $(this).data('id');
                const gameRating = $(this).data('rating');
                const gameReview = $(this).data('review');
                const currentHours = $(this).data('currenthours'); // Get current hours

                $('#gameId').val(gameId);
                $('#modalRating').val(gameRating);
                $('#modalReview').val(gameReview);
                $('#modalHours').val(0); // Reset additional hours field
                $('#editModal').fadeIn();
            });

            // Close the modal
            $('#closeModal').on('click', function () {
                $('#editModal').fadeOut();
            });

            // Save changes via AJAX
            $('#saveChanges').on('click', function () {
                const formData = $('#editForm').serialize();

                $.ajax({
                    url: 'templates/edit_game_ajax.php', // This points to your AJAX handler
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response === "success") {
                            alert('Changes saved successfully!');
                            location.reload(); // Refresh the page to update data
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            // Delete game via AJAX
            $('.delete-button').on('click', function () {
                const gameId = $(this).data('id');

                if (confirm("Are you sure you want to delete this game?")) {
                    $.ajax({
                        url: 'templates/delete_game_ajax.php', // PHP file that handles deletion
                        type: 'POST',
                        data: { game_id: gameId },
                        success: function (response) {
                            if (response === "success") {
                                alert("Game deleted successfully.");
                                location.reload(); // Refresh the page
                            } else {
                                alert("An error occurred. Please try again.");
                            }
                        },
                        error: function () {
                            alert("An error occurred. Please try again.");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
