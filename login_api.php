<?php
header('Content-Type: application/json');
session_start();
include("db.php");

$response = array('success' => false, 'message' => '', 'redirect' => '');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }

    $email = trim($data["ST_email_address"]);
    $password = $data["ST_password"];
    $remember = isset($data["remember"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address.";
        echo json_encode($response);
        exit;
    }

    if (empty($password)) {
        $response['message'] = "Password is required.";
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT * FROM student WHERE ST_email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        if (password_verify($password, $student["ST_password"])) {
            if ($student['status'] === 'approved') {
                // Set session variables
                $_SESSION['registration_nbr'] = $student['registration_nbr'];
                $_SESSION['student_name'] = $student['ST_first_name'];
                $_SESSION['status'] = $student['status'];
                $_SESSION['user_type'] = 'student';

                // Handle remember me
                if ($remember) {
                    setcookie("ST_email_address", $email, time() + (86400 * 30), "/");
                    setcookie("ST_password", $password, time() + (86400 * 30), "/");
                }

                $response['success'] = true;
                $response['message'] = "Login successful!";
                $response['redirect'] = "studentFAQ.php";
            } elseif ($student['status'] === 'pending') {
                $response['message'] = "Your registration is still pending admin approval.";
            } else {
                $response['message'] = "Your registration has been rejected.";
            }
        } else {
            $response['message'] = "Incorrect email or password.";
        }
    } else {
        $response['message'] = "Incorrect email or password.";
    }
    $stmt->close();
}

$conn->close();
echo json_encode($response);
?> 