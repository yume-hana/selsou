<?php
session_start();
require_once 'db.php';

// إعداد الهيدر والرسائل JSON
header('Content-Type: application/json');

// إظهار الأخطاء (لأغراض التصحيح فقط)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// التحقق من تسجيل الدخول
if (!isset($_SESSION['registration_nbr'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to access questions.'
    ]);
    exit;
}


$studentId = $_SESSION['registration_nbr'];

// توليد token إذا لم يكن موجودًا
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// ✅ إرسال سؤال جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_question') {
    $content = trim($_POST['contentQ'] ?? '');

    if (strlen($content) < 5) {
        echo json_encode(['status' => 'error', 'message' => 'Question too short.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO message_quest (contentQ, quest_date, quest_time, state) 
                            VALUES (?, CURDATE(), CURTIME(), 'pending')");
    $stmt->bind_param("s", $content);

    if ($stmt->execute()) {
        $questNbr = $conn->insert_id;

        $relationStmt = $conn->prepare("INSERT INTO ask (registration_nbr, quest_nbr) VALUES (?, ?)");
        $relationStmt->bind_param("si", $studentId, $questNbr);

        if ($relationStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Question added successfully.']);
        } else {
            // حذف السؤال إذا فشل الربط
            $deleteStmt = $conn->prepare("DELETE FROM message_quest WHERE quest_nbr = ?");
            $deleteStmt->bind_param("i", $questNbr);
            $deleteStmt->execute();
            $deleteStmt->close();

            echo json_encode(['status' => 'error', 'message' => 'Failed to link question to student.']);
        }

        $relationStmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert question.']);
    }

    $stmt->close();
    exit;
}

// ✅ عرض الأسئلة
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filter = $_GET['filter'] ?? 'all';

    $query = "SELECT mq.* FROM message_quest mq 
              JOIN ask a ON mq.quest_nbr = a.quest_nbr 
              WHERE a.registration_nbr = ?";

    if ($filter === 'answered') {
        $query .= " AND mq.state = 'answered'";
    } elseif ($filter === 'unanswered') {
        $query .= " AND mq.state = 'pending'";
    }

    $query .= " ORDER BY mq.quest_nbr DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];

    while ($row = $result->fetch_assoc()) {
        $answer = null;

        // فقط إذا السؤال مجاوب نبحث عن الإجابة
        if ($row['state'] === 'answered') {
            $answerStmt = $conn->prepare("SELECT contentA FROM message_answer WHERE quest_nbr = ?");
            $answerStmt->bind_param("i", $row['quest_nbr']);
            $answerStmt->execute();
            $answerResult = $answerStmt->get_result();
            $answerRow = $answerResult->fetch_assoc();
            $answer = $answerRow['contentA'] ?? null;
            $answerStmt->close();
        }

        $questions[] = [
            'id' => $row['quest_nbr'],
            'number' => $row['quest_nbr'],
            'content' => $row['contentQ'],
            'date' => $row['quest_date'],
            'time' => $row['quest_time'],
            'status' => $row['state'],
            'answer' => $answer // ✅ عرض الإجابة هنا
        ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $questions
    ]);

    $stmt->close();
    exit;
}

//  في حال لم يتطابق أي شرط
echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
?>
