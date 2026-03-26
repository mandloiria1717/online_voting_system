<?php
// ============================================================
// includes/config.php - Database Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'voting_system');

// Create MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:40px;color:#c0392b;background:#fdf0f0;border-left:4px solid #e74c3c;margin:20px;border-radius:8px;'>
        <h2>⚠ Database Connection Failed</h2>
        <p>" . $conn->connect_error . "</p>
        <p>Please ensure MySQL is running and the database <strong>voting_system</strong> exists.</p>
        <p>Run <code>database.sql</code> in phpMyAdmin first.</p>
    </div>");
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
