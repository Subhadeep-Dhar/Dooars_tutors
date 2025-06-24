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
    $profession_type = $_POST['profession_type'] ?? ''; // art_teacher, visual_arts
    $art_type = $_POST['art_type'] ?? ''; // Drawing, Painting, Sculpture, etc.
    $gender = $_POST['gender'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $days_per_week = $_POST['days_per_week'] ?? '';

    // Base SQL query - focus on art related professions
    $sql = "
        SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession_details,
               t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
        FROM tutors t
        WHERE t.status = 'active' 
        AND t.profession_details IS NOT NULL 
        AND t.profession_details != ''
        AND (
            JSON_EXTRACT(t.profession_details, '$.art_teacher') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.visual_arts') IS NOT NULL
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
            case 'art_teacher':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.art_teacher') IS NOT NULL";
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.art_teacher.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'visual_arts':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.visual_arts') IS NOT NULL";
                
                if (!empty($art_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.visual_arts.type')) LIKE ?";
                    $params[] = "%$art_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.visual_arts.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.visual_arts.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;
        }
    }

    // General filters that work across all art professions
    if (empty($profession_type)) {
        // If no specific profession type, allow general searches across all art fields
        
        if (!empty($art_type)) {
            $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.visual_arts.type')) LIKE ?";
            $params[] = "%$art_type%";
        }
        
        if (!empty($gender)) {
            $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.visual_arts.gender')) LIKE ?";
            $params[] = "%$gender%";
        }
        
        if (!empty($days_per_week)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.art_teacher.days_per_week')) = ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.visual_arts.days_per_week')) = ?
            )";
            $params[] = $days_per_week;
            $params[] = $days_per_week;
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
            
            // Extract art teacher data
            if (isset($professionDetails['art_teacher'])) {
                $teacher['professions']['art_teacher'] = $professionDetails['art_teacher'];
            }
            
            // Extract visual arts data
            if (isset($professionDetails['visual_arts'])) {
                $teacher['professions']['visual_arts'] = $professionDetails['visual_arts'];
            }
            
            // Create a summary of available services
            $services = [];
            if (isset($professionDetails['art_teacher'])) {
                $services[] = 'Art Teacher';
            }
            if (isset($professionDetails['visual_arts'])) {
                $artType = $professionDetails['visual_arts']['type'] ?? 'N/A';
                $services[] = 'Visual Arts (' . $artType . ')';
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