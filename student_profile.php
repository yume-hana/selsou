<?php
session_start();
include("db.php");
include("auth.php");

$student_id = $_SESSION['registration_nbr'];
$success_message = "";
$error_message = "";

// Upload profile image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] != UPLOAD_ERR_NO_FILE) {
    $target_dir = "profile_images/";
    if (!file_exists($target_dir)) {

        mkdir($target_dir, 0777, true);
    }

    $image_name = basename($_FILES["profile_image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($image_file_type, $allowed_types)) {
        $error_message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        $upload_ok = 0;
    }

    if ($upload_ok && move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        $filename = basename($target_file);
        $sql = "UPDATE student SET profile_image = ? WHERE registration_nbr = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $filename, $student_id);
        $stmt->execute();
        $stmt->close();
        $success_message = "Profile image updated successfully.";
    }
}

// Update student information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $gender = $_POST['ST_gender'];
    $nationality = $_POST['ST_Nationality'];
    $dob = $_POST['date_of_birth'];
    $address = $_POST['ST_address'];

    $sql = "UPDATE student SET ST_gender=?, ST_Nationality=?, date_of_birth=?, ST_address=? WHERE registration_nbr=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $gender, $nationality, $dob, $address, $student_id);

    if ($stmt->execute()) {
        $success_message = "Personal information updated successfully.";
    } else {
        $error_message = "Error updating information: " . $conn->error;
    }
    $stmt->close();
}

// Fetch student data
$sql = "SELECT ST_first_name, ST_last_name, ST_email_address, ST_gender, ST_Nationality, date_of_birth, ST_address, profile_image 
        FROM student WHERE registration_nbr = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Default profile image if not set
$profile_image = !empty($student['profile_image']) ? "profile_images/" . htmlspecialchars($student['profile_image']) : "profile_images/user.jpg";
include 'profilest2.php';
?>
