<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost:3307';
$dbname = 'dooars_tutors';
$username = 'root';  // Replace with actual username
$password = '';  // Replace with actual password

try {
    // Database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch only active tutors
    $stmt = $pdo->prepare("
        SELECT id, name, phone, email, experience, boards, classes, subjects, 
               teaching_preferences, city, address, latitude, longitude, 
               plan, status, type, created_at, rating, rating_count
        FROM tutors 
        WHERE status = 'active' 
        ORDER BY rating DESC, rating_count DESC
    ");
    
    $stmt->execute();
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'tutors' => $tutors,
        'count' => count($tutors)
    ]);
    
} catch(PDOException $e) {
    // Log the actual error for debugging (don't expose to client)
    error_log("Database error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch tutors. Please try again later.'
    ]);
}
?>