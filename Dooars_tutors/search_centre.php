<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost:3307';
$dbname = 'dooars_tutors';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get search parameters
    $name = $_POST['name'] ?? '';
    $board = $_POST['board'] ?? '';
    $city = $_POST['city'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $class = $_POST['classGrade'] ?? '';
    
    // Build the SQL query
    $sql = "SELECT id, name, phone, email, experience, boards, classes, subjects, 
                   teaching_preferences, city, address, rating, rating_count, status 
            FROM tutors WHERE status = 'active' AND profession = 'tutor'";
    
    $params = array();
    $conditions = array();
    
    // Add conditions based on search parameters
    if (!empty($name)) {
        $conditions[] = "name LIKE ?";
        $params[] = "%$name%";
    }
    
    if (!empty($board)) {
        $conditions[] = "boards LIKE ?";
        $params[] = "%$board%";
    }
    
    if (!empty($city)) {
        $conditions[] = "city LIKE ?";
        $params[] = "%$city%";
    }
    
    if (!empty($subject)) {
        $conditions[] = "subjects LIKE ?";
        $params[] = "%$subject%";
    }
    
    if (!empty($class)) {
        $conditions[] = "classes LIKE ?";
        $params[] = "%$class%";
    }
    
    // Add conditions to the query
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    
    // Add ordering
    $sql .= " ORDER BY rating DESC, rating_count DESC";
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the results
    foreach ($teachers as &$teacher) {
        $teacher['rating'] = number_format($teacher['rating'], 1);
        $teacher['experience'] = $teacher['experience'] ?: 'Not specified';
    }
    
    echo json_encode($teachers);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>