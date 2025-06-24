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
    $profession_type = $_POST['profession_type'] ?? ''; // sports_coach, sports_coaching_centre, etc.
    $sports_type = $_POST['sports_type'] ?? ''; // Cricket, Football, Basketball, etc.
    $gender = $_POST['gender'] ?? '';
    $days_per_week = $_POST['days_per_week'] ?? '';

    // Base SQL query - focus on sports related professions
    $sql = "
        SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession_details,
               t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
        FROM tutors t
        WHERE t.status = 'active' 
        AND t.profession_details IS NOT NULL 
        AND t.profession_details != ''
        AND (
            JSON_EXTRACT(t.profession_details, '$.sports_coach') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre') IS NOT NULL
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

    // Profession type specific filters
    if (!empty($profession_type)) {
        switch ($profession_type) {
            case 'sports_coach':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.sports_coach') IS NOT NULL";
                
                if (!empty($sports_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coach.sports_type')) LIKE ?";
                    $params[] = "%$sports_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coach.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coach.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'sports_coaching_centre':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre') IS NOT NULL";
                
                if (!empty($sports_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre.sports_type')) LIKE ?";
                    $params[] = "%$sports_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;
        }
    }

    // General filters that work across all sports professions
    if (empty($profession_type)) {
        // If no specific profession type, allow general searches across all sports fields
        
        if (!empty($sports_type)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coach.sports_type')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre.sports_type')) LIKE ?
            )";
            $params[] = "%$sports_type%";
            $params[] = "%$sports_type%";
        }
        
        if (!empty($gender)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coach.gender')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre.gender')) LIKE ?
            )";
            $params[] = "%$gender%";
            $params[] = "%$gender%";
        }
        
        if (!empty($days_per_week)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coach.days_per_week')) = ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.sports_coaching_centre.days_per_week')) = ?
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
            
            // Extract sports coach data
            if (isset($professionDetails['sports_coach'])) {
                $teacher['professions']['sports_coach'] = $professionDetails['sports_coach'];
            }
            
            // Extract sports coaching centre data
            if (isset($professionDetails['sports_coaching_centre'])) {
                $teacher['professions']['sports_coaching_centre'] = $professionDetails['sports_coaching_centre'];
            }
            
            // Create a summary of available services
            $services = [];
            if (isset($professionDetails['sports_coach'])) {
                $sports_type = $professionDetails['sports_coach']['sports_type'] ?? 'N/A';
                $gender = $professionDetails['sports_coach']['gender'] ?? 'N/A';
                $services[] = "Sports Coach ($sports_type - $gender)";
            }
            if (isset($professionDetails['sports_coaching_centre'])) {
                $sports_type = $professionDetails['sports_coaching_centre']['sports_type'] ?? 'N/A';
                $gender = $professionDetails['sports_coaching_centre']['gender'] ?? 'N/A';
                $services[] = "Sports Coaching Centre ($sports_type - $gender)";
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