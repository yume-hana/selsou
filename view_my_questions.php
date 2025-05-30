<?php
session_start(); // أول شيء
include("auth.php"); // التحقق من تسجيل الدخول
include("db.php");

// نتأكد أن المستخدم هو طالب
if ($_SESSION['user_type'] !== 'student') {
    echo "<p style='color:red;'>Access Denied.</p>";
    exit;
}

$registration_nbr = $_SESSION['registration_nbr'];

// نجيب الأسئلة الخاصة بالطالب باستخدام العلاقة بين ask و message_quest
$sql = "
    SELECT mq.quest_nbr, mq.contentQ, mq.quest_date, mq.quest_time, mq.state
    FROM message_quest AS mq
    INNER JOIN ask AS a ON mq.quest_nbr = a.quest_nbr
    WHERE a.registration_nbr = '$registration_nbr'
    ORDER BY mq.quest_date DESC, mq.quest_time DESC
";
// استعملنا a , q باش نفرقوا لانو عندنا رقم السؤال في كلا الجدولين

$result = mysqli_query($conn, $sql);

?>
