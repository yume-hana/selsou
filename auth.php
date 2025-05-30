<?php
file_put_contents("auth_test.txt", "AUTH WORKING");

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/my_error_log.log'); // يسجل في نفس مجلد auth.php
error_log("== TEST LOG FROM AUTH FILE ==");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("Auth check - Session contents: " . print_r($_SESSION, true));

if (!isset($_SESSION['registration_nbr']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    error_log("Auth check failed - Redirecting to login");
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}
?>
