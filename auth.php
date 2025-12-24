<?php
session_start();

// Function to check if user is logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Function to check if user is logged in (for UI toggles)
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>