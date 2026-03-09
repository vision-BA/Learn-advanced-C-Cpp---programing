<?php
/**
 * Configuration File - SIMS (Student Information Management System)
 * All configuration settings in one place
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'Bright@2025');
define('DB_NAME', 'sims_db');
define('DB_PORT', 3306);

// Application Settings
define('APP_NAME', 'Student Information Management System');
define('APP_URL', 'http://localhost/sims');
define('APP_VERSION', '1.0.0');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('REMEMBER_ME_DURATION', 604800); // 7 days in seconds

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'));
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/sims/uploads/');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Email Configuration (Optional - for future email notifications)
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_PORT', 465);
define('MAIL_USER', 'your_email@example.com');
define('MAIL_PASSWORD', 'your_password');
define('MAIL_FROM', 'noreply@sims.com');
define('MAIL_FROM_NAME', 'SIMS');

// Security Settings
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 10);
define('ENABLE_HTTPS', false); // Set to true in production
define('CSRF_TOKEN_LENGTH', 32);

// Timezone
define('DEFAULT_TIMEZONE', 'UTC');
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/sims/logs/error.log');

// Define roles
define('ROLE_ADMIN', 'admin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_STUDENT', 'student');

// Define status
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');

// Grades Scale
$GRADES_SCALE = array(
    '90' => 'A',
    '80' => 'B',
    '70' => 'C',
    '60' => 'D',
    '0' => 'F'
);

// Create upload directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Create logs directory if it doesn't exist
if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/sims/logs')) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . '/sims/logs', 0755, true);
}
