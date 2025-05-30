<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

try {
    // Get question details
    $questionStmt = $pdo->prepare("
        SELECT mq.*, s.registration_nbr 
        FROM message_quest mq
        JOIN ask a ON mq.quest_nbr = a.quest_nbr
        JOIN student s ON a.Registration_nbr = s.registration_nbr
        WHERE mq.quest_nbr = :quest_nbr
        AND s.Tutor_ID = :tutor_id
    ");
    
    $questionStmt->execute([
        ':quest_nbr' => $_GET['quest_nbr'],
        ':tutor_id' => $_SESSION['tutor_id']
    ]);
    
    $question = $questionStmt->fetch();

    if (!$question) {
        die("Question not found or unauthorized access");
    }

    // Handle answer submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pdo->beginTransaction();
        
        // Insert answer
        $answerStmt = $pdo->prepare("
            INSERT INTO message_answer (contentA, ans_date, ans_time)
            VALUES (:content, CURDATE(), CURTIME())
        ");
        $answerStmt->execute([':content' => $_POST['answer']]);
        $answerCode = $pdo->lastInsertId();

        // Associate with question
        $associateStmt = $pdo->prepare("
            INSERT INTO associate (answer_code, ques_nbr)
            VALUES (:answer_code, :quest_nbr)
        ");
        $associateStmt->execute([
            ':answer_code' => $answerCode,
            ':quest_nbr' => $_GET['quest_nbr']
        ]);

        // Record sending
        $sendStmt = $pdo->prepare("
            INSERT INTO send (Tutor_ID, answer_code, date_sent, time_sent)
            VALUES (:tutor_id, :answer_code, CURDATE(), CURTIME())
        ");
        $sendStmt->execute([
            ':tutor_id' => $_SESSION['tutor_id'],
            ':answer_code' => $answerCode
        ]);

        // Update question status
        $updateStmt = $pdo->prepare("
            UPDATE message_quest 
            SET state = 'answered' 
            WHERE quest_nbr = :quest_nbr
        ");
        $updateStmt->execute([':quest_nbr' => $_GET['quest_nbr']]);

        $pdo->commit();
        header("Location: questions.php?answered=1");
        exit();
    }
} catch(PDOException $e) {
    $pdo->rollBack();
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Answer Question</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Answer Question</h1>
        <a href="questions.php">Back to Questions</a>

        <div class="question">
            <p><?= htmlspecialchars($question['contentQ']) ?></p>
            <small>From student #<?= $question['registration_nbr'] ?></small>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Your Answer:</label>
                <textarea name="answer" required rows="5"></textarea>
            </div>
            <button type="submit" class="btn-submit">Submit Answer</button>
        </form>
    </div>
</body>
</html>