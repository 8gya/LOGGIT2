<?php
session_start(); // Make sure to start the session before destroying it

if (isset($_GET['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Optionally, you can also delete the session cookie to ensure it's completely cleared
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"]
        );
    }

    // Redirect to the homepage (or anywhere else you want)
    header("Location: ../index.php");
    exit();
}
?>
