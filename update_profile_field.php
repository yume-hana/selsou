<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['registration_nbr'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$registration_nbr = $_SESSION['registration_nbr'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example: update name
    if (isset($_POST['update_type']) && $_POST['update_type'] === 'name') {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);

        if (empty($first_name) || empty($last_name)) {
            echo json_encode(['success' => false, 'message' => 'Both names are required.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE student SET ST_first_name = ?, ST_last_name = ? WHERE registration_nbr = ?");
        $stmt->bind_param("sss", $first_name, $last_name, $registration_nbr);
    }

    // Example: update email
    elseif ($_POST['update_type'] === 'email') {
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE student SET ST_email_address = ? WHERE registration_nbr = ?");
        $stmt->bind_param("ss", $email, $registration_nbr);
    }

    // Example: update address
    elseif ($_POST['update_type'] === 'address') {
        $address = trim($_POST['address']);

        if (strlen($address) > 200) {
            echo json_encode(['success' => false, 'message' => 'Address too long.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE student SET ST_address = ? WHERE registration_nbr = ?");
        $stmt->bind_param("ss", $address, $registration_nbr);
    }

    else {
        echo json_encode(['success' => false, 'message' => 'Invalid update type']);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }

    $stmt->close();
    $conn->close();
}
?>
