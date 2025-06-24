<?php
session_start();
require_once 'config.php';

$tutor_id = $_GET['tutor_id'] ?? null;
if (!$tutor_id) {
    header('Location: register.php');
    exit;
}

// Fetch tutor details
$query = "SELECT * FROM tutors WHERE id = ? AND payment_status = 'paid'";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$tutor = $result->fetch_assoc();

if (!$tutor) {
    header('Location: register.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registration Successful - DooarsTutors</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            text-align: center;
            border: 1px solid #28a745;
            border-radius: 10px;
            background: #d4edda;
            font-family: Arial, sans-serif;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h2>Registration Successful!</h2>
        <p>Welcome to DooarsTutors! Your registration has been completed successfully.</p>
        
        <div class="details">
            <h3>Registration Details</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($tutor['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($tutor['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($tutor['phone']); ?></p>
            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($tutor['payment_id']); ?></p>
            <p><strong>Registration Date:</strong> <?php echo date('d M Y', strtotime($tutor['payment_date'])); ?></p>
        </div>
        
        <p>You will receive a confirmation email shortly with your login details.</p>
        
        <a href="index.php" class="btn">Go to Home</a>
        <a href="login.php" class="btn">Login</a>
    </div>
</body>
</html>