<?php
session_start();
include("auth.php");
include("db.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    echo "Access Denied.";
    exit();
}

$registration_nbr = $_SESSION['registration_nbr'];

// نسجل زيارة الطالب لكل سؤال في جدول consult (باستخدام استعلام محضر)
$sql = "
    SELECT f.FAQ_nbr, f.question_content, f.answer_question, 
           CONCAT(t.first_nameT, ' ', t.last_nameT) AS tutor_fullname
    FROM quesfaq f
    JOIN tutor t ON f.Tutor_ID = t.Tutor_ID
    ORDER BY f.FAQ_nbr DESC
";

$result = mysqli_query($conn, $sql);

// نتحقق إذا كان الطالب قد استشار السؤال مسبقاً قبل أن نقوم بإدخال الزيارة في جدول consult
while ($row = mysqli_fetch_assoc($result)) {
    $faq_id = $row['FAQ_nbr'];

    // نتحقق هل الطالب استشار هذا السؤال من قبل
    $check_query = $conn->prepare("SELECT * FROM consult WHERE FAQ_nbr = ? AND registration_nbr = ?");
    $check_query->bind_param("ii", $faq_id, $registration_nbr);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows == 0) {
        // إذا لم يكن قد استشار هذا السؤال من قبل، نقوم بإدخال الزيارة
        $now_date = date("Y-m-d");
        $now_time = date("H:i:s");
        $insert_query = $conn->prepare("INSERT INTO consult (FAQ_nbr, registration_nbr, date_consult, time_consult) VALUES (?, ?, ?, ?)");
        $insert_query->bind_param("iiss", $faq_id, $registration_nbr, $now_date, $now_time);
        $insert_query->execute();
    }
}

// نرجع نستعلم البيانات من جديد بعد ما تحرك المؤشر
$result = mysqli_query($conn, $sql);
?>