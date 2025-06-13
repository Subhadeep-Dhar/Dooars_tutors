<?php
// Database configuration
$host = 'localhost:3307'; // Change this to your database host
$dbname = 'dooars_tutors'; // Change this to your database name
$username = 'root'; // Change this to your database username
$password = ''; // Change this to your database password

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect and sanitize form data
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Handle checkbox arrays
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
        
        // Handle teaching preferences (mode and location)
        $teaching_mode = isset($_POST['mode']) ? implode(',', $_POST['mode']) : '';
        $preferred_location = isset($_POST['preferred_location']) ? implode(',', $_POST['preferred_location']) : '';
        $teaching_preferences = $teaching_mode . '|' . $preferred_location; // Combining mode and location
        
        // Location data
        $city = trim($_POST['city'] ?? ''); // You might need to add city field to form or extract from citySelect
        $address = trim($_POST['address'] ?? '');
        $latitude = floatval($_POST['latitude'] ?? 0);
        $longitude = floatval($_POST['longitude'] ?? 0);
        
        // Default values for missing fields
        $experience = ''; // Not in form, you might want to add this field
        $plan = 'basic'; // Default plan since no payment gateway
        $status = 'active'; // Default status
        $type = 'tutor'; // Default type
        
        // Validation
        if (empty($name) || empty($phone) || empty($address)) {
            throw new Exception('Required fields are missing: Name, Phone, and Address are mandatory.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            throw new Exception('Invalid email format.');
        }
        
        // Prepare SQL statement
        $sql = "INSERT INTO tutors (
            name, phone, email, experience, boards, classes, subjects, 
            teaching_preferences, city, address, latitude, longitude, 
            plan, status, type, created_at
        ) VALUES (
            :name, :phone, :email, :experience, :boards, :classes, :subjects,
            :teaching_preferences, :city, :address, :latitude, :longitude,
            :plan, :status, :type, NOW()
        )";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':experience', $experience);
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
                'redirect' => 'index.php' // Change this to your success page
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