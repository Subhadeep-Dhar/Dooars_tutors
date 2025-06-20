<?php
// Enable error reporting during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set response type to JSON
header('Content-Type: application/json');

// Database configuration
$host = 'localhost:3307';
$dbname = 'dooars_tutors';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST parameters
    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $org_type = trim($_POST['org_type'] ?? '');
    $course_type = trim($_POST['course_type'] ?? '');
    $days_per_week = trim($_POST['days_per_week'] ?? '');

    // Base SQL query for organization type tutors
    $sql = "
    SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession, t.profession_details,
        t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
    FROM tutors t
    WHERE t.status = 'active'
        AND t.type = 'Organisation'
        AND (
            JSON_CONTAINS_PATH(t.profession_details, 'one', '$.educational_coaching_centre')
            OR JSON_CONTAINS_PATH(t.profession_details, 'one', '$.computer_centre')
            OR JSON_CONTAINS_PATH(t.profession_details, 'one', '$.abacus_centre')
        )
";


    $params = [];
    $conditions = [];

    // Apply filters
    if (!empty($name)) {
        $conditions[] = "t.name LIKE ?";
        $params[] = "%$name%";
    }

    if (!empty($city)) {
        $conditions[] = "t.city LIKE ?";
        $params[] = "%$city%";
    }

    if (!empty($org_type)) {
        $conditions[] = "FIND_IN_SET(?, t.profession) > 0";
        $params[] = $org_type;
    }

    // More flexible JSON filtering for course_type and days_per_week
    if (!empty($course_type)) {
        $conditions[] = "(
            JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.educational_coaching_centre.course_type')) LIKE ? OR
            JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.computer_centre.course_type')) LIKE ? OR
            JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.abacus_centre.course_type')) LIKE ?
        )";
        $params[] = "%$course_type%";
        $params[] = "%$course_type%";
        $params[] = "%$course_type%";
    }

    if (!empty($days_per_week)) {
        $conditions[] = "(
            JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.educational_coaching_centre.days_per_week')) = ? OR
            JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.computer_centre.days_per_week')) = ? OR
            JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.abacus_centre.days_per_week')) = ?
        )";
        $params[] = $days_per_week;
        $params[] = $days_per_week;
        $params[] = $days_per_week;
    }

    // Add dynamic conditions
    if (!empty($conditions)) {
        $sql .= ' AND ' . implode(' AND ', $conditions);
    }

    // Add ordering
    $sql .= " ORDER BY t.rating DESC, t.rating_count DESC";

    // Prepare and execute
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process results to ensure proper data structure
    $processedResults = [];
    foreach ($results as $result) {
        // Ensure rating is numeric
        $result['rating'] = floatval($result['rating'] ?? 0);
        $result['rating_count'] = intval($result['rating_count'] ?? 0);
        $result['experience'] = intval($result['experience'] ?? 0);
        
        // Validate JSON in profession_details
        if (!empty($result['profession_details'])) {
            $decoded = json_decode($result['profession_details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If JSON is invalid, create a default structure
                $result['profession_details'] = json_encode([]);
            }
        } else {
            $result['profession_details'] = json_encode([]);
        }
        
        $processedResults[] = $result;
    }

    echo json_encode([
        'success' => true,
        'results' => $processedResults,
        'count' => count($processedResults)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database Error: ' . $e->getMessage(),
        'results' => []
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'results' => []
    ]);
}
?>