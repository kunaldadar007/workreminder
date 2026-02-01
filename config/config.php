<?php
/**
 * Application Configuration
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

// Start session
session_start();

// Application settings
define('APP_NAME', 'Work Reminder and Chat Bot System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/workreminder/');

// Security settings
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_LIFETIME', 3600); // 1 hour

// Notification settings
define('NOTIFICATION_SOUND', 'assets/sounds/notification.mp3');
define('REMINDER_CHECK_INTERVAL', 30); // seconds

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'database.php';

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}
?>
