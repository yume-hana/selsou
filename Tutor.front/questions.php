<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

try {
    $stmt = $pdo->prepare("
        SELECT mq.*, s.ST_first_name, s.ST_last_name 
        FROM message_quest mq
        JOIN ask a ON mq.quest_nbr = a.quest_nbr
        JOIN student s ON a.Registration_nbr = s.registration_nbr
        WHERE s.Tutor_ID = :tutor_id
        AND mq.state != 'answered'
        ORDER BY mq.quest_date DESC
    ");
    $stmt->execute([':tutor_id' => $_SESSION['tutor_id']]);
    $questions = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Questions</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Pending Questions</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php if (!empty($questions)): ?>
            <div class="question-list">
                <?php foreach ($questions as $q): ?>
                <div class="question">
                    <h3>From <?= htmlspecialchars($q['ST_first_name']) ?>:</h3>
                    <p><?= htmlspecialchars($q['contentQ']) ?></p>
                    <small>Asked on <?= $q['quest_date'] ?> at <?= $q['quest_time'] ?></small>
                    <a href="answer.php?quest_nbr=<?= $q['quest_nbr'] ?>" class="btn-answer">Answer</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No pending questions.</p>
        <?php endif; ?>
    </div>
</body>
</html>