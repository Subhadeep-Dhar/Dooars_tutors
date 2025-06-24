<?php
// logout.php - Comprehensive logout script

// Start session
session_start();

// Update database with logout time (optional)
if (isset($_SESSION['phone'])) {
    try {
        // Database configuration
        $servername = "localhost:3307";
        $username = "root";
        $password = "";
        $dbname = "dooars_tutors";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
        } else {
            // Update last logout time
            $stmt = $conn->prepare("UPDATE tutors SET last_logout = NOW() WHERE phone = ?");
            $stmt->bind_param("s", $_SESSION['phone']);
            $stmt->execute();
            $stmt->close();
        }
        
        $conn->close();
    } catch (Exception $e) {
        error_log("Logout database update error: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page with logout message
header("Location: login.php?logout=success");
exit();
?>