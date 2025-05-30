<!-- the file of functions.php -->
<?php
require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/db.php';

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['tutor_id']);
}

function tutorOnly() {
    if (!isLoggedIn()) {
        $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
        redirect('/LMW-PROJET/Tutor.front/TutorHome.html');
    }
}
?>