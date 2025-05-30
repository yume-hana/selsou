<?php
// cancel_meeting.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database connection
require_once 'db.php';

// Get JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

// Debug log
error_log("Received data: " . print_r($data, true));

// Validate required fields
if (
    !isset($data['registration_nbr']) ||
    !isset($data['Tutor_ID']) ||
    !isset($data['Meeting_date']) ||
    empty($data['registration_nbr']) ||
    empty($data['Tutor_ID']) ||
    empty($data['Meeting_date'])
) {
    http_response_code(400);
    echo json_encode([
        "error" => "Missing required fields.",
        "received" => $data
    ]);
    exit;
}

// Sanitize inputs
$registration_nbr = htmlspecialchars(strip_tags($data['registration_nbr']));
$tutor_id = htmlspecialchars(strip_tags($data['Tutor_ID']));
$meeting_date = htmlspecialchars(strip_tags($data['Meeting_date']));

// Log sanitized inputs
error_log("Sanitized inputs - registration_nbr: $registration_nbr, tutor_id: $tutor_id, meeting_date: $meeting_date");

// Check if meeting exists
$checkQuery = "SELECT * FROM meeting WHERE registration_nbr = ? AND Tutor_ID = ? AND Meeting_date = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("sss", $registration_nbr, $tutor_id, $meeting_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "error" => "Meeting not found.",
        "params" => [
            "registration_nbr" => $registration_nbr,
            "tutor_id" => $tutor_id,
            "meeting_date" => $meeting_date
        ]
    ]);
    exit;
}

// Update meeting status to canceled
$updateQuery = "UPDATE meeting SET state_MT = 'canceled' WHERE registration_nbr = ? AND Tutor_ID = ? AND Meeting_date = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("sss", $registration_nbr, $tutor_id, $meeting_date);

if ($updateStmt->execute()) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Meeting canceled successfully."]);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to cancel meeting.",
        "db_error" => $conn->error
    ]);
}

$conn->close();
?>