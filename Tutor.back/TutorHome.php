<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/auth_check.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>الصفحة الرئيسية للمُدرس</title>
</head>
<body>
    <h1>مرحبا بيك، <?= htmlspecialchars($_SESSION['tutor_name']) ?>!</h1>
    <p>هذا هو المحتوى الخاص بك.</p>
</body>
</html>
