<?php
// config.php - Add this configuration file

// Database Configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$database = 'dooars_tutors';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Razorpay Configuration
define('RAZORPAY_KEY_ID', 'rzp_test_gfO2l4EpREF1nE');
define('RAZORPAY_KEY_SECRET', 'YrVSGFdrn4nQdPPPhfZnfoVH');

// Other Configuration
define('REGISTRATION_FEE', 500); // You can change this dynamically
define('CURRENCY', 'INR');
define('COMPANY_NAME', 'DooarsTutors');
?>