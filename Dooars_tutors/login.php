<?php
// Start session at the very beginning
session_start();

// Check if user is already logged in and redirect
if (isset($_SESSION['phone']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: t_panel.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .container { max-width: 400px; margin: 0 auto; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; cursor: pointer; }
        button:hover { background: #005a87; }
        .link { color: #007cba; text-decoration: underline; cursor: pointer; margin: 10px 0; display: block; }
        .form { display: none; }
        .form.active { display: block; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Login Form -->
        <div id="loginForm" class="form active">
            <h2>Login</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <span class="link" onclick="showForm('forgotForm')">Forgot Password?</span>
            <span class="link" onclick="showForm('registerForm')">Register Now</span>
        </div>

        <!-- Forgot Password Form -->
        <div id="forgotForm" class="form">
            <h2>Forgot Password</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="forgot">
                <input type="tel" name="phone" placeholder="Registered Phone Number" required>
                <button type="submit">Send Password</button>
            </form>
            <span class="link" onclick="showForm('loginForm')">Back to Login</span>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="form">
            <h2>Register</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="register">
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
            <span class="link" onclick="showForm('loginForm')">Back to Login</span>
        </div>
    </div>

    <script>
        function showForm(formId) {
            document.querySelectorAll('.form').forEach(form => form.classList.remove('active'));
            document.getElementById(formId).classList.add('active');
        }
    </script>

    <?php
    // Database configuration
    $servername = "localhost:3307";
    $username = "root";
    $password = "";
    $dbname = "dooars_tutors";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'];
        $phone = $_POST['phone'];

        if ($action == 'login') {
    $password = $_POST['password'];

    // Step 1: Check in admin table
    $admin_stmt = $conn->prepare("SELECT * FROM admin WHERE phone = ?");
    $admin_stmt->bind_param("s", $phone);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();

    if ($admin_result->num_rows > 0) {
        $admin = $admin_result->fetch_assoc();

        if ($admin['password'] === $password) {
            // Login successful for admin
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['phone'] = $phone;
            $_SESSION['role'] = 'admin';
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "<div class='message error'>Invalid phone number or password!</div>";
        }

        $admin_stmt->close();
    } else {
        // Step 2: Check in tutors table
        $tutor_stmt = $conn->prepare("SELECT * FROM tutors WHERE phone = ?");
        $tutor_stmt->bind_param("s", $phone);
        $tutor_stmt->execute();
        $tutor_result = $tutor_stmt->get_result();

        if ($tutor_result->num_rows > 0) {
            $tutor = $tutor_result->fetch_assoc();

            if ($tutor['password'] === $password) {
                // Login successful for tutor
                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['phone'] = $phone;
                $_SESSION['role'] = 'tutor';
                $_SESSION['user_id'] = $tutor['id'] ?? null;

                // Optionally update login time
                $update_stmt = $conn->prepare("UPDATE tutors SET last_login = NOW() WHERE phone = ?");
                $update_stmt->bind_param("s", $phone);
                $update_stmt->execute();
                $update_stmt->close();

                header("Location: t_panel.php");
                exit();
            } else {
                echo "<div class='message error'>Invalid phone number or password!</div>";
            }

        } else {
            echo "<div class='message error'>Invalid phone number or password!</div>";
        }

        $tutor_stmt->close();
    }
}

        
        elseif ($action == 'forgot') {
            $stmt = $conn->prepare("SELECT password FROM tutors WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_password = $row['password'];
                
                // SMS API call (replace with your SMS provider)
                $sms_message = "Your password is: " . $user_password;
                // sendSMS($phone, $sms_message); // Implement your SMS function
                
                // WhatsApp API call (replace with your WhatsApp provider)
                // sendWhatsApp($phone, $sms_message); // Implement your WhatsApp function
                
                echo "<div class='message success'>Password sent to your phone via SMS and WhatsApp!</div>";
            } else {
                echo "<div class='message error'>Phone number not registered!</div>";
            }
            $stmt->close();
        }
        
        elseif ($action == 'register') {
            $password = $_POST['password'];
            
            // Check if phone already exists
            $stmt = $conn->prepare("SELECT * FROM tutors WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<div class='message error'>Phone number already registered!</div>";
            } else {
                // Insert new user with registration timestamp
                $stmt = $conn->prepare("INSERT INTO tutors (phone, password, created_at) VALUES (?, ?, NOW())");
                $stmt->bind_param("ss", $phone, $password);
                
                if ($stmt->execute()) {
                    echo "<div class='message success'>Registration successful! You can now login.</div>";
                } else {
                    echo "<div class='message error'>Registration failed!</div>";
                }
            }
            $stmt->close();
        }
    }

    $conn->close();

    // SMS Function (implement with your SMS provider)
    function sendSMS($phone, $message) {
        // Example with Twilio, MSG91, or any SMS provider
        // Replace with your SMS API credentials and endpoint
        /*
        $api_key = "your_sms_api_key";
        $sender_id = "your_sender_id";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.msg91.com/api/sendhttp.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'authkey' => $api_key,
                'mobiles' => $phone,
                'message' => $message,
                'sender' => $sender_id,
                'route' => '4'
            ))
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
        */
    }

    // WhatsApp Function (implement with your WhatsApp provider)
    function sendWhatsApp($phone, $message) {
        // Example with WhatsApp Business API
        // Replace with your WhatsApp API credentials and endpoint
        
        /* $api_key = "9083009315";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.whatsapp.com/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode(array(
                'phone' => $phone,
                'message' => $message,
                'api_key' => $api_key
            )),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        return $response; */
       
    }
    ?>
</body>
</html>