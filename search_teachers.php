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

    // Base SQL query - focus on profession_details JSON  AND payment_status = 'paid'
    $sql = "
        SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession_details,
               t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
        FROM tutors t
        WHERE t.status = 'active' AND t.profession_details IS NOT NULL AND t.profession_details != ''
    ";

    $params = [];
    $conditions = [];

    // Dynamic filters
    if (!empty($name)) {
        $conditions[] = "t.name LIKE ?";
        $params[] = "%$name%";
    }

    if (!empty($city)) {
        $conditions[] = "t.city LIKE ?";
        $params[] = "%$city%";
    }

    // Board search in JSON
    if (!empty($board)) {
        $conditions[] = "JSON_EXTRACT(t.profession_details, '$.tutor.boards') LIKE ?";
        $params[] = "%$board%";
    }

    // Subject search in JSON
    if (!empty($subject)) {
        $subjects = array_filter(array_map('trim', explode(',', $subject)));
        $subjectConditions = [];
        
        foreach ($subjects as $subj) {
            if (!empty($subj)) {
                $subjectConditions[] = "JSON_EXTRACT(t.profession_details, '$.tutor.subjects') LIKE ?";
                $params[] = "%$subj%";
            }
        }
        
        if (!empty($subjectConditions)) {
            $conditions[] = "(" . implode(" AND ", $subjectConditions) . ")";
        }
    }

    // Class search in JSON
    if (!empty($class)) {
        $classes = array_filter(array_map('trim', explode(',', $class)));
        $classConditions = [];
        
        foreach ($classes as $cls) {
            if (!empty($cls)) {
                $cleanClass = preg_replace('/^class\s*/i', '', $cls);
                $classConditions[] = "JSON_EXTRACT(t.profession_details, '$.tutor.classes') LIKE ?";
                $params[] = "%$cleanClass%";
            }
        }
        
        if (!empty($classConditions)) {
            $conditions[] = "(" . implode(" OR ", $classConditions) . ")";
        }
    }

    // Append remaining conditions
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    // Sorting
    $sql .= " ORDER BY t.rating DESC, t.rating_count DESC";

    // Execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enhanced result formatting - extract data from JSON
    foreach ($teachers as &$teacher) {
        $teacher['rating'] = number_format($teacher['rating'], 1);
        $teacher['experience'] = $teacher['experience'] ?: 'Not specified';
        
        // Parse profession_details JSON
        if (!empty($teacher['profession_details'])) {
            $professionDetails = json_decode($teacher['profession_details'], true);
            
            // Extract tutor data from JSON
            if (isset($professionDetails['tutor'])) {
                $tutorData = $professionDetails['tutor'];
                $teacher['boards'] = $tutorData['boards'] ?? '';
                $teacher['classes'] = $tutorData['classes'] ?? '';
                $teacher['subjects'] = $tutorData['subjects'] ?? '';
                $teacher['class_subject_mapping'] = $tutorData['class_subject_mapping'] ?? [];
            } else {
                $teacher['boards'] = '';
                $teacher['classes'] = '';
                $teacher['subjects'] = '';
                $teacher['class_subject_mapping'] = [];
            }
        } else {
            $teacher['boards'] = '';
            $teacher['classes'] = '';
            $teacher['subjects'] = '';
            $teacher['class_subject_mapping'] = [];
        }
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