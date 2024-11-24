<?php
session_start(); // Start the session

// Check if the staff session exists
if (isset($_SESSION['staff'])) {
    // Unset the staff session variable
    unset($_SESSION['staff']);

    // Optionally, clear all session data for the staff
    // session_write_close(); // Ensure the session is saved before redirecting

    // Redirect to the login page
    header("Location: ../index.php");
    exit;
} else {
    // If no staff is logged in, redirect to the login page
    header("Location: ../index.php");
    exit;
}
?>
