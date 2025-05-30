<?php
session_start();
include("db.php");
include("auth.php");

header('Content-Type: application/json');

$student_id = $_SESSION['registration_nbr'] ?? '';

if (empty($student_id)) {
    echo json_encode(['error' => 'Unauthorized access. Please login.']);
    exit;
}

// دالة لتحديد لون الحالة
function getColor($state) {
    return match($state) {
        'pending'     => 'LightBlue',
        'accepted'    => 'Green',
        'rescheduled' => 'Blue Green',
        'canceled'    => 'Red',
        'rejected'    => 'Orange',
        'completed'   => 'Yellow',
        default       => 'Black', // في حال جات حالة غير معروفة
    };
}

// إلغاء الموعد إذا تم الضغط على زر cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_meeting'])) {
    $tutor_id = $_POST['Tutor_ID'] ?? null;
    $meeting_date = $_POST['Meeting_date'] ?? null;

    if (!$tutor_id || !$meeting_date) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit;
    }

    $sql_cancel = "UPDATE meeting 
                   SET state_MT = 'canceled' 
                   WHERE registration_nbr = ? AND Tutor_ID = ? AND Meeting_date = ?";
    $stmt_cancel = $conn->prepare($sql_cancel);
    $stmt_cancel->bind_param("sss", $student_id, $tutor_id, $meeting_date);
    $stmt_cancel->execute();
    $stmt_cancel->close();

    echo json_encode(['success' => true, 'message' => 'Appointment canceled successfully.']);
    exit;
}

// استرجاع المواعيد
$sql = "SELECT DISTINCT Tutor_ID, Meeting_date, Meeting_time, Meeting_location, content_MT, state_MT
        FROM meeting
        WHERE registration_nbr = ?
        ORDER BY Meeting_date DESC, Meeting_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];

while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        'title' => $row['content_MT'],
        'start' => $row['Meeting_date'] . 'T' . $row['Meeting_time'],
        'backgroundColor' => getColor($row['state_MT']),
        'borderColor' => getColor($row['state_MT']),
        'extendedProps' => [
            'status' => $row['state_MT'],
            'place' => $row['Meeting_location'],
            'tutor_id' => $row['Tutor_ID']
        ]
    ];
}

echo json_encode($appointments);
exit;
?>
