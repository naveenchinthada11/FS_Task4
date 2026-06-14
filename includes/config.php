<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore');

// Application Settings
define('APP_NAME', 'Online Bookstore');
define('APP_URL', 'http://localhost/myproject/Task4');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
