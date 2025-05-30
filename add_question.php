<?php
session_start(); // أول شيء
include("db.php");
include("auth.php");

$message = "";


if (!isset($_SESSION['registration_nbr'])) {
    header("Location: login.php");
    exit;
}

 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // حماية إضافية من الإرسال العشوائي
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        $message = "<div class='form-message error'>Invalid submission. Please reload the page and try again.</div>";
    } else {
        $student_id = $_SESSION["registration_nbr"];
        $content = trim($_POST["contentQ"]);

        if (empty($content)) {
            $message = "<div class='form-message error'>Question content cannot be empty.</div>";
        } elseif (strlen($content) < 10 || strlen($content) > 1000) {
            $message = "<div class='form-message error'>Question must be between 10 and 1000 characters.</div>";
        } else {
            $check_sql = "SELECT * FROM ask 
                        JOIN message_quest ON ask.quest_nbr = message_quest.quest_nbr
                        WHERE ask.registration_nbr = ? AND message_quest.contentQ = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $student_id, $content);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $message = "<div class='form-message warning'>You already submitted this question before.</div>";
            } else {
                $insert_sql = "INSERT INTO message_quest (contentQ, quest_date, quest_time, state)
                            VALUES (?, CURDATE(), CURTIME(), 'pending')";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("s", $content);

                if ($stmt->execute()) {
                    $question_id = $stmt->insert_id;

                    $link_sql = "INSERT INTO ask (registration_nbr, quest_nbr) VALUES (?, ?)";
                    $link_stmt = $conn->prepare($link_sql);
                    $link_stmt->bind_param("si", $student_id, $question_id);

                    if ($link_stmt->execute()) {
                        $message = "<div class='form-message success'>Your question has been submitted successfully!</div>";
                    } else {
                        $message = "<div class='form-message error'>Error linking student to question: " . $link_stmt->error . "</div>";
                    }
                    $link_stmt->close();
                } else {
                    $message = "<div class='form-message error'>Error submitting question: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
    }
    $conn->close();
}
?>
