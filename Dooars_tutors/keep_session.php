<?php
// keep_session.php - Optional script to keep session active

// Include session check
require_once 'check_session.php';

// Set content type
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action']) && $input['action'] === 'ping') {
        // Update last activity time
        $_SESSION['last_activity'] = time();
        
        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Session updated',
            'last_activity' => $_SESSION['last_activity'],
            'phone' => $_SESSION['phone']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST requests allowed'
    ]);
}
?>