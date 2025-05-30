<?php
// request_meeting.php - Enhanced version with better error handling

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database connection
require_once 'db.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get JSON data from request
$request_body = file_get_contents('php://input');
error_log("Raw request data: " . $request_body);

$data = json_decode($request_body, true);
error_log("Decoded data: " . print_r($data, true));

// Validate required fields
if (
    !isset($data['registration_nbr']) || 
    !isset($data['Tutor_Id']) || 
    !isset($data['Meeting_date']) ||
    !isset($data['Meeting_time']) ||
    !isset($data['content_MT'])
) {
    http_response_code(400);
    echo json_encode([
        "error" => "Missing required fields",
        "received" => $data
    ]);
    exit;
}

// Sanitize inputs
$registration_nbr = htmlspecialchars(strip_tags($data['registration_nbr']));
$tutor_id = htmlspecialchars(strip_tags($data['Tutor_Id']));
$meeting_date = htmlspecialchars(strip_tags($data['Meeting_date']));
$meeting_time = htmlspecialchars(strip_tags($data['Meeting_time']));
$meeting_location = isset($data['Meeting_location']) ? htmlspecialchars(strip_tags($data['Meeting_location'])) : '';
$content_MT = htmlspecialchars(strip_tags($data['content_MT']));
$state_MT = isset($data['state_MT']) ? htmlspecialchars(strip_tags($data['state_MT'])) : 'pending';

// Check for existing meeting
$check_query = "SELECT * FROM meeting WHERE registration_nbr = ? AND Tutor_ID = ? AND Meeting_date = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("sss", $registration_nbr, $tutor_id, $meeting_date);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["error" => "Meeting already exists for this date, tutor and student"]);
    exit;
}

// Insert new meeting
$insert_query = "INSERT INTO meeting (registration_nbr, Tutor_ID, Meeting_date, Meeting_time, Meeting_location, content_MT, state_MT) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("sssssss", 
    $registration_nbr, 
    $tutor_id, 
    $meeting_date, 
    $meeting_time, 
    $meeting_location, 
    $content_MT, 
    $state_MT
);

try {
    if ($insert_stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode([
            "success" => true,
            "message" => "Meeting created successfully"
        ]);
    } else {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to create meeting",
        "details" => $e->getMessage()  // هذي ترجع سبب الخطأ الحقيقي
    ]);
}

$conn->close();
?>