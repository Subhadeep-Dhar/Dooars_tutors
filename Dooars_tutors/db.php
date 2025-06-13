<?php
// db.php — Database connection file

$host = 'localhost:3307';
$user = 'root';
$password = '';
$dbname = 'dooars_tutors';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set
$conn->set_charset("utf8mb4");
?>
