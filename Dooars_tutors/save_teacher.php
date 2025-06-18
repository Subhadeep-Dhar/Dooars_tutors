<?php
// Database configuration
$host = 'localhost:3307';
$dbname = 'dooars_tutors';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect and sanitize basic form data
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Handle profession array (multiple selections allowed)
        $profession = isset($_POST['profession']) ? implode(',', $_POST['profession']) : '';
        
        // Handle tutor-specific fields (only if Tutor is selected)
        $boards = '';
        $classes = '';
        $subjects = '';
        
        if (in_array('Tutor', $_POST['profession'] ?? [])) {
            $boards = isset($_POST['boards']) ? implode(',', $_POST['boards']) : '';
            $classes = isset($_POST['classes']) ? implode(',', $_POST['classes']) : '';
            $subjects = isset($_POST['subjects']) ? implode(',', $_POST['subjects']) : '';
            
            // Handle other class specification
            if (in_array('others', $_POST['classes'] ?? []) && !empty($_POST['other_class'])) {
                $classes = str_replace('others', trim($_POST['other_class']), $classes);
            }
            
            // Handle other subject specification
            if (in_array('Others', $_POST['subjects'] ?? []) && !empty($_POST['other_subject'])) {
                $subjects = str_replace('Others', trim($_POST['other_subject']), $subjects);
            }
        }
        
        // Handle profession-specific details
        $profession_details = [];
        
        // Sports Coach details
        if (in_array('Sports Coach', $_POST['profession'] ?? [])) {
            $profession_details['sports_coach'] = [
                'sports_type' => trim($_POST['sports_type'] ?? ''),
                'gender' => trim($_POST['sports_gender'] ?? ''),
                'days_per_week' => trim($_POST['sports_days'] ?? '')
            ];
        }
        
        // Trainer details
        if (in_array('Trainer', $_POST['profession'] ?? [])) {
            $profession_details['trainer'] = [
                'training_type' => trim($_POST['training_type'] ?? ''),
                'gender' => trim($_POST['training_gender'] ?? ''),
                'days_per_week' => trim($_POST['training_days'] ?? '')
            ];
        }
        
        // Dance Teacher details
        if (in_array('Dance Teacher', $_POST['profession'] ?? [])) {
            $profession_details['dance_teacher'] = [
                'dance_type' => trim($_POST['dance_type'] ?? ''),
                'gender' => trim($_POST['dance_gender'] ?? ''),
                'days_per_week' => trim($_POST['dance_days'] ?? '')
            ];
        }
        
        // Music Teacher details
        if (in_array('Music Teacher', $_POST['profession'] ?? [])) {
            $profession_details['music_teacher'] = [
                'music_type' => trim($_POST['music_type'] ?? ''),
                'gender' => trim($_POST['music_gender'] ?? ''),
                'days_per_week' => trim($_POST['music_days'] ?? '')
            ];
        }
        
        // Singing Teacher details
        if (in_array('Singing Teacher', $_POST['profession'] ?? [])) {
            $profession_details['singing_teacher'] = [
                'singing_type' => trim($_POST['singing_type'] ?? ''),
                'gender' => trim($_POST['singing_gender'] ?? ''),
                'days_per_week' => trim($_POST['singing_days'] ?? '')
            ];
        }
        
        // Art Teacher details
        if (in_array('Art Teacher', $_POST['profession'] ?? [])) {
            $profession_details['art_teacher'] = [
                'days_per_week' => trim($_POST['art_days'] ?? '')
            ];
        }
        
        // Others details
        if (in_array('others', $_POST['profession'] ?? [])) {
            $profession_details['others'] = [
                'profession_name' => trim($_POST['other_profession_name'] ?? ''),
                'gender' => trim($_POST['other_gender'] ?? ''),
                'days_per_week' => trim($_POST['other_days'] ?? '')
            ];
        }
        
        // Convert profession details to JSON for storage
        $profession_details_json = json_encode($profession_details);
        
        // Handle teaching preferences (mode and location) - for tutors
        $teaching_mode = isset($_POST['mode']) ? implode(',', $_POST['mode']) : '';
        $preferred_location = isset($_POST['preferred_location']) ? implode(',', $_POST['preferred_location']) : '';
        $teaching_preferences = $teaching_mode . '|' . $preferred_location;
        
        // Location data
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $latitude = floatval($_POST['latitude'] ?? 0);
        $longitude = floatval($_POST['longitude'] ?? 0);
        
        // Other fields
        $experience = trim($_POST['experience'] ?? '');
        $plan = 'basic';
        $status = 'active';
        $type = strtolower(trim($_POST['user_type'] ?? 'individual'));
        if (!in_array($type, ['individual', 'organization'])) {
            $type = 'individual';
        }
        
        // Validation
        if (empty($name) || empty($phone) || empty($address) || empty($profession)) {
            throw new Exception('Required fields are missing: Name, Phone, Address, and Profession are mandatory.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            throw new Exception('Invalid email format.');
        }
        
        // Additional validation for profession-specific fields
        foreach ($_POST['profession'] as $prof) {
            switch ($prof) {
                case 'Sports Coach':
                    if (empty($_POST['sports_type']) || empty($_POST['sports_gender']) || empty($_POST['sports_days'])) {
                        throw new Exception('Sports Coach requires: Sports Type, Gender, and Days per week.');
                    }
                    break;
                case 'Trainer':
                    if (empty($_POST['training_type']) || empty($_POST['training_gender']) || empty($_POST['training_days'])) {
                        throw new Exception('Trainer requires: Training Type, Gender, and Days per week.');
                    }
                    break;
                case 'Dance Teacher':
                    if (empty($_POST['dance_type']) || empty($_POST['dance_gender']) || empty($_POST['dance_days'])) {
                        throw new Exception('Dance Teacher requires: Dance Type, Gender, and Days per week.');
                    }
                    break;
                case 'Music Teacher':
                    if (empty($_POST['music_type']) || empty($_POST['music_gender']) || empty($_POST['music_days'])) {
                        throw new Exception('Music Teacher requires: Music Type, Gender, and Days per week.');
                    }
                    break;
                case 'Singing Teacher':
                    if (empty($_POST['singing_type']) || empty($_POST['singing_gender']) || empty($_POST['singing_days'])) {
                        throw new Exception('Singing Teacher requires: Singing Type, Gender, and Days per week.');
                    }
                    break;
                case 'Art Teacher':
                    if (empty($_POST['art_days'])) {
                        throw new Exception('Art Teacher requires: Days per week.');
                    }
                    break;
                case 'others':
                    if (empty($_POST['other_profession_name']) || empty($_POST['other_gender']) || empty($_POST['other_days'])) {
                        throw new Exception('Other profession requires: Profession Name, Gender, and Days per week.');
                    }
                    break;
            }
        }
        
        // Prepare SQL statement with new profession_details field
        $sql = "INSERT INTO tutors (
            name, phone, email, experience, profession, profession_details, boards, classes, subjects, 
            teaching_preferences, city, address, latitude, longitude, 
            plan, status, type, created_at
        ) VALUES (
            :name, :phone, :email, :experience, :profession, :profession_details, :boards, :classes, :subjects,
            :teaching_preferences, :city, :address, :latitude, :longitude,
            :plan, :status, :type, NOW()
        )";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':experience', $experience);
        $stmt->bindParam(':profession', $profession);
        $stmt->bindParam(':profession_details', $profession_details_json);
        $stmt->bindParam(':boards', $boards);
        $stmt->bindParam(':classes', $classes);
        $stmt->bindParam(':subjects', $subjects);
        $stmt->bindParam(':teaching_preferences', $teaching_preferences);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':plan', $plan);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':type', $type);
        
        // Execute the statement
        if ($stmt->execute()) {
            $tutor_id = $pdo->lastInsertId();
            
            // Success response
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful! Welcome to DooarsTutors.',
                'tutor_id' => $tutor_id,
                'redirect' => 'index.php'
            ]);
        } else {
            throw new Exception('Failed to save registration data.');
        }
        
    } else {
        throw new Exception('Invalid request method.');
    }
    
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    // General error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connection
$pdo = null;
?>