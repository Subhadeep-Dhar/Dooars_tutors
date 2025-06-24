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
    $profession_type = $_POST['profession_type'] ?? ''; // music_teacher, singing_teacher, abriti_school, etc.
    $music_type = $_POST['music_type'] ?? ''; // Eastern/Classical, Western, etc.
    $singing_type = $_POST['singing_type'] ?? '';
    $instruments = $_POST['instruments'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $days_per_week = $_POST['days_per_week'] ?? '';

    // Base SQL query - focus on music, singing, and abriti related professions
    $sql = "
        SELECT t.id, t.name, t.phone, t.email, t.experience, t.profession_details,
               t.teaching_preferences, t.city, t.address, t.rating, t.rating_count, t.status
        FROM tutors t
        WHERE t.status = 'active' 
        AND t.profession_details IS NOT NULL 
        AND t.profession_details != ''
        AND (
            JSON_EXTRACT(t.profession_details, '$.music_teacher') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.singing_teacher') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.music_school') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.singing_school') IS NOT NULL
            OR JSON_EXTRACT(t.profession_details, '$.abriti_school') IS NOT NULL
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
            case 'music_teacher':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.music_teacher') IS NOT NULL";
                
                if (!empty($music_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_teacher.music_type')) LIKE ?";
                    $params[] = "%$music_type%";
                }
                
                if (!empty($instruments)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_teacher.instruments')) LIKE ?";
                    $params[] = "%$instruments%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_teacher.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'singing_teacher':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.singing_teacher') IS NOT NULL";
                
                if (!empty($singing_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_teacher.singing_type')) LIKE ?";
                    $params[] = "%$singing_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_teacher.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_teacher.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'music_school':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.music_school') IS NOT NULL";
                
                if (!empty($music_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_school.music_type')) LIKE ?";
                    $params[] = "%$music_type%";
                }
                
                if (!empty($instruments)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_school.instrument')) LIKE ?";
                    $params[] = "%$instruments%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_school.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'singing_school':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.singing_school') IS NOT NULL";
                
                if (!empty($singing_type)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_school.singing_type')) LIKE ?";
                    $params[] = "%$singing_type%";
                }
                
                if (!empty($gender)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_school.gender')) LIKE ?";
                    $params[] = "%$gender%";
                }
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_school.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;

            case 'abriti_school':
                $conditions[] = "JSON_EXTRACT(t.profession_details, '$.abriti_school') IS NOT NULL";
                
                if (!empty($days_per_week)) {
                    $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.abriti_school.days_per_week')) = ?";
                    $params[] = $days_per_week;
                }
                break;
        }
    }

    // General filters that work across all music/singing professions
    if (empty($profession_type)) {
        // If no specific profession type, allow general searches across all music/singing fields
        
        if (!empty($music_type)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_teacher.music_type')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_school.music_type')) LIKE ?
            )";
            $params[] = "%$music_type%";
            $params[] = "%$music_type%";
        }
        
        if (!empty($singing_type)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_teacher.singing_type')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_school.singing_type')) LIKE ?
            )";
            $params[] = "%$singing_type%";
            $params[] = "%$singing_type%";
        }
        
        if (!empty($instruments)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_teacher.instruments')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.music_school.instrument')) LIKE ?
            )";
            $params[] = "%$instruments%";
            $params[] = "%$instruments%";
        }
        
        if (!empty($gender)) {
            $conditions[] = "(
                JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_teacher.gender')) LIKE ?
                OR JSON_UNQUOTE(JSON_EXTRACT(t.profession_details, '$.singing_school.gender')) LIKE ?
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
            
            // Extract music teacher data
            if (isset($professionDetails['music_teacher'])) {
                $teacher['professions']['music_teacher'] = $professionDetails['music_teacher'];
            }
            
            // Extract singing teacher data
            if (isset($professionDetails['singing_teacher'])) {
                $teacher['professions']['singing_teacher'] = $professionDetails['singing_teacher'];
            }
            
            // Extract music school data
            if (isset($professionDetails['music_school'])) {
                $teacher['professions']['music_school'] = $professionDetails['music_school'];
            }
            
            // Extract singing school data
            if (isset($professionDetails['singing_school'])) {
                $teacher['professions']['singing_school'] = $professionDetails['singing_school'];
            }
            
            // Extract abriti school data
            if (isset($professionDetails['abriti_school'])) {
                $teacher['professions']['abriti_school'] = $professionDetails['abriti_school'];
            }
            
            // Create a summary of available services
            $services = [];
            if (isset($professionDetails['music_teacher'])) {
                $services[] = 'Music Teacher (' . ($professionDetails['music_teacher']['music_type'] ?? 'N/A') . ')';
            }
            if (isset($professionDetails['singing_teacher'])) {
                $services[] = 'Singing Teacher (' . ($professionDetails['singing_teacher']['singing_type'] ?? 'N/A') . ')';
            }
            if (isset($professionDetails['music_school'])) {
                $services[] = 'Music School (' . ($professionDetails['music_school']['music_type'] ?? 'N/A') . ')';
            }
            if (isset($professionDetails['singing_school'])) {
                $services[] = 'Singing School (' . ($professionDetails['singing_school']['singing_type'] ?? 'N/A') . ')';
            }
            if (isset($professionDetails['abriti_school'])) {
                $services[] = 'Abriti School';
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