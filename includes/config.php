<?php
// includes/config.php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'watch_sk_fund');

/* Attempt to connect to MySQL database */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SMTP Settings for Password Reset (PHPMailer)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 1025); // Mailpit or Mailhog default
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', ''); // 'tls', 'ssl', or ''
define('MAIL_FROM', 'noreply@watchskfund.gov.ph');
define('MAIL_FROM_NAME', 'Watch SK Fund Portal');
?>
