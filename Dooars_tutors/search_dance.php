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
    $city = $_POST['city'] ?? '';
    $profession_type = $_POST['profession_type'] ?? ''; // dance_teacher, dance_school
    $dance_type = $_POST['dance_type'] ?? ''; // Classical, Western, Folk, etc.
    $gender = $_POST['gender'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $days_per_week = $_POST['days_per_week'] ?? '';

    // Base SQL query - focus on dance related professions
    $sql = "
        SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession_details,
               t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
        FROM tutors t
        WHERE t.status = 'active' 
        AND t.profession_details IS NOT NULL 
        AND t.profession_details != ''
        AND (
            JSON_EXTRACT(t.profession_details, '$.dance_teacher') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.dance_school') IS NOT NULL
        )
    ";

    $params = [];
    $conditions = [];

    // Basic filters
    if (!empty($name)) {
        $conditions[] = "t.name LIKE ?";
        $params[] = "%$name%";
    }

    if (!empty($city)) {
        $conditions[] = "t.city LIKE ?";
        $params[] = "%$city%";
    }

    if (!empty($experience)) {
        $conditions[] = "t.experience LIKE ?";
        $params[] = "%$experience%";
    }

    // Profession type specific filters
    if (!empty($profession_type)) {
        switch ($profession_type) {
            case 'dance_teacher':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.dance_teacher') IS NOT NULL";
                
                if (!empty($dance_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_teacher.dance_type')) LIKE ?";
                    $params[] = "%$dance_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_teacher.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_teacher.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'dance_school':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.dance_school') IS NOT NULL";
                
                if (!empty($dance_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_school.dance_type')) LIKE ?";
                    $params[] = "%$dance_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_school.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_school.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;
        }
    }

    // General filters that work across all dance professions
    if (empty($profession_type)) {
        // If no specific profession type, allow general searches across all dance fields
        
        if (!empty($dance_type)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_teacher.dance_type')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_school.dance_type')) LIKE ?
            )";
            $params[] = "%$dance_type%";
            $params[] = "%$dance_type%";
        }
        
        if (!empty($gender)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_teacher.gender')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.dance_school.gender')) LIKE ?
            )";
            $params[] = "%$gender%";
            $params[] = "%$gender%";
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
            
            // Initialize profession arrays
            $teacher['professions'] = [];
            
            // Extract dance teacher data
            if (isset($professionDetails['dance_teacher'])) {
                $teacher['professions']['dance_teacher'] = $professionDetails['dance_teacher'];
            }
            
            // Extract dance school data
            if (isset($professionDetails['dance_school'])) {
                $teacher['professions']['dance_school'] = $professionDetails['dance_school'];
            }
            
            // Create a summary of available services
            $services = [];
            if (isset($professionDetails['dance_teacher'])) {
                $services[] = 'Dance Teacher (' . ($professionDetails['dance_teacher']['dance_type'] ?? 'N/A') . ')';
            }
            if (isset($professionDetails['dance_school'])) {
                $services[] = 'Dance School (' . ($professionDetails['dance_school']['dance_type'] ?? 'N/A') . ')';
            }
            
            $teacher['available_services'] = implode(', ', $services);
            
        } else {
            $teacher['professions'] = [];
            $teacher['available_services'] = '';
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