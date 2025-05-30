<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['registration_nbr'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['registration_nbr'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        // التحقق من الحجم (2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            echo "Image is too large. Max size is 2MB.";
            exit;
        }

        // التحقق من نوع الملف الحقيقي
        $mimeType = mime_content_type($file['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($mimeType, $allowedTypes)) {
            echo "Invalid file type. Only JPG and PNG allowed.";
            exit;
        }

        // التحقق من أن الملف صورة فعلًا
        if (!getimagesize($file['tmp_name'])) {
            echo "The file is not a valid image.";
            exit;
        }

        $uploadDir = '/profile_images';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('img_', true) . '.' . $extension;
        $uploadPath = $uploadDir . basename($newFileName);

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // حذف الصورة القديمة إن وُجدت
            $stmt_old = $conn->prepare("SELECT profile_image FROM student WHERE registration_nbr = ?");
            $stmt_old->bind_param("s", $student_id);
            $stmt_old->execute();
            $stmt_old->bind_result($old_image);
            if ($stmt_old->fetch() && !empty($old_image) && file_exists($old_image)) {
                unlink($old_image); // حذف الملف
            }
            $stmt_old->close();

            // حفظ الصورة الجديدة
            $stmt = $conn->prepare("UPDATE student SET profile_image = ? WHERE registration_nbr = ?");
            $stmt->bind_param("ss", $uploadPath, $student_id);
            if ($stmt->execute()) {
                header("Location: student_profile.php?success=1");
                exit;
            } else {
                echo "Error updating database.";
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "File upload error.";
    }
}
?>
