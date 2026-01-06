<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
        http_response_code(403);
        include 'access_guard.php';
        exit;
    }

    // Setup error handling and logging
    ini_set('display_errors', 1); // Set to 0 in production
    ini_set('log_errors', 1);
    error_reporting(E_ALL);

    // Ensure log directory exists
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    ini_set('error_log', "$logDir/php_errors.log");

    date_default_timezone_set('Asia/Manila');

    // Start session once only
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>
