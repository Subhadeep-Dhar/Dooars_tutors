<?php
// check_session.php - Include this file at the top of protected pages

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function checkLogin() {
    // Check if essential session variables exist
    if (!isset($_SESSION['logged_in']) || !isset($_SESSION['phone']) || $_SESSION['logged_in'] !== true) {
        // User is not logged in, redirect to login page
        header("Location: login.php");
        exit();
    }
    
    // Optional: Check session timeout (uncomment if you want session expiration)
    /*
    if (isset($_SESSION['expire_time']) && time() > $_SESSION['expire_time']) {
        // Session expired
        session_destroy();
        header("Location: login.php?expired=1");
        exit();
    }
    */
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    return true;
}

// Function to get current user phone
function getCurrentUserPhone() {
    return isset($_SESSION['phone']) ? $_SESSION['phone'] : null;
}

// Function to get current user ID (if available)
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Function to check how long user has been logged in
function getSessionDuration() {
    if (isset($_SESSION['login_time'])) {
        return time() - $_SESSION['login_time'];
    }
    return 0;
}

// Automatically check login when this file is included
checkLogin();
?>