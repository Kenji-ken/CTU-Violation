<?php
session_start(); // Start the session

// Check if the admin session exists
if (isset($_SESSION['admin'])) {
    // Unset the admin session variable
    unset($_SESSION['admin']);
    // session_destroy(); // Destroy the session completely

    header('Location: ../index.php');

}

// Optionally, return a response to confirm the logout
echo json_encode(['status' => 'logged_out']);
?>
