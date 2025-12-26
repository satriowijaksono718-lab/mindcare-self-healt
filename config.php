<?php
// Determine environment
$env = getenv('APP_ENV') ?: 'local';

// Database Configuration
if ($env === 'production') {
    // Production: Use Supabase PostgreSQL or remote MySQL
    $db_host = getenv('DB_HOST');
    $db_user = getenv('DB_USER');
    $db_pass = getenv('DB_PASS');
    $db_name = getenv('DB_NAME');
    $base_url = getenv('BASE_URL');
} else {
    // Local development
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'mindcare_db');
    define('BASE_URL', 'http://localhost/mindcare1/');
}

// Create connection
$conn = new mysqli($db_host ?? DB_HOST, $db_user ?? DB_USER, $db_pass ?? DB_PASS, $db_name ?? DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Define base URL if not already defined
if (!defined('BASE_URL')) {
    define('BASE_URL', $base_url);
}
?>
