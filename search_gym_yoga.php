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
    $profession_type = $_POST['profession_type'] ?? ''; // trainer, gym_yoga
    $training_type = $_POST['training_type'] ?? ''; // Gym, Yoga
    $gender = $_POST['gender'] ?? '';
    $days_per_week = $_POST['days_per_week'] ?? '';

    // Base SQL query - focus on gym and yoga related professions
    $sql = "
        SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession_details,
               t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
        FROM tutors t
        WHERE t.status = 'active' 
        AND t.profession_details IS NOT NULL 
        AND t.profession_details != ''
        AND (
            JSON_EXTRACT(t.profession_details, '$.trainer') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.gym_yoga') IS NOT NULL
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
            case 'trainer':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.trainer') IS NOT NULL";
                
                if (!empty($training_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.trainer.training_type')) LIKE ?";
                    $params[] = "%$training_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.trainer.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.trainer.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'gym_yoga':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.gym_yoga') IS NOT NULL";
                
                if (!empty($training_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.gym_yoga.training_type')) LIKE ?";
                    $params[] = "%$training_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.gym_yoga.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.gym_yoga.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;
        }
    }

    // General filters that work across all gym/yoga professions
    if (empty($profession_type)) {
        // If no specific profession type, allow general searches across all gym/yoga fields
        
        if (!empty($training_type)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.trainer.training_type')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.gym_yoga.training_type')) LIKE ?
            )";
            $params[] = "%$training_type%";
            $params[] = "%$training_type%";
        }
        
        if (!empty($gender)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.trainer.gender')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.gym_yoga.gender')) LIKE ?
            )";
            $params[] = "%$gender%";
            $params[] = "%$gender%";
        }
        
        if (!empty($days_per_week)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.trainer.days_per_week')) = ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.gym_yoga.days_per_week')) = ?
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
            
            // Extract trainer data
            if (isset($professionDetails['trainer'])) {
                $teacher['professions']['trainer'] = $professionDetails['trainer'];
            }
            
            // Extract gym_yoga data
            if (isset($professionDetails['gym_yoga'])) {
                $teacher['professions']['gym_yoga'] = $professionDetails['gym_yoga'];
            }
            
            // Create a summary of available services
            $services = [];
            if (isset($professionDetails['trainer'])) {
                $training_type = $professionDetails['trainer']['training_type'] ?? 'N/A';
                $gender = $professionDetails['trainer']['gender'] ?? 'N/A';
                $services[] = "Trainer ($training_type - $gender)";
            }
            if (isset($professionDetails['gym_yoga'])) {
                $training_type = $professionDetails['gym_yoga']['training_type'] ?? 'N/A';
                $gender = $professionDetails['gym_yoga']['gender'] ?? 'N/A';
                $services[] = "Gym/Yoga ($training_type - $gender)";
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