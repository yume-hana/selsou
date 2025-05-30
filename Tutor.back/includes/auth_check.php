<!-- the file of auth_check.php -->
<?php
require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/functions.php';
file_put_contents("auth_test.txt", "AUTH WORKING");

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/my_error_log.log');
error_log("== TEST LOG FROM AUTH FILE ==");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("Auth check - Session contents: " . print_r($_SESSION, true));
// if (empty($_SESSION['tutor_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'tutor') {
//     error_log("Auth check failed - Redirecting to login");
//     $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
//     header("Location: http://localhost/LMW-PROJET/Tutor.back/login.php");
//     exit;
// }