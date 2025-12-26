<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mindcare_db');
define('DB_PORT', 3306);

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8mb4");

// Define base URL
define('BASE_URL', 'http://localhost/mindcare1/');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
