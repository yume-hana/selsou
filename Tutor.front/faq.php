<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = '';

// Handle FAQ submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = sanitizeInput($_POST['question']);
    $answer = sanitizeInput($_POST['answer']);

    if (empty($question)) $errors[] = "Question is required";
    if (empty($answer)) $errors[] = "Answer is required";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO QuesFAQ (question_content, answer_question, Tutor_ID) 
                                   VALUES (?, ?, ?)");
            $stmt->execute([$question, $answer, $_SESSION['tutor_id']]);
            $success = "FAQ added successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error adding FAQ: " . $e->getMessage();
        }
    }
}

// Get all FAQs
try {
    $faqStmt = $pdo->prepare("
        SELECT q.*, t.first_nameT, t.last_nameT 
        FROM QuesFAQ q
        JOIN tutor t ON q.Tutor_ID = t.Tutor_ID
        ORDER BY created_at DESC
    ");
    $faqStmt->execute();
    $faqs = $faqStmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>FAQ Management</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .faq-item {
            margin: 1rem 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 1rem;
        }
        .faq-question {
            font-weight: bold;
            cursor: pointer;
        }
        .faq-answer {
            display: none;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: #f8f9fa;
        }
        .faq-meta {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>FAQ Management</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Add FAQ Form (for tutors) -->
        <div class="faq-form">
            <h2>Add New FAQ</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Question:</label>
                    <textarea name="question" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Answer:</label>
                    <textarea name="answer" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn">Submit FAQ</button>
            </form>
        </div>

        <!-- FAQ List -->
        <div class="faq-list">
            <h2>Existing FAQs</h2>
            <?php if (empty($faqs)): ?>
                <p>No FAQs found.</p>
            <?php else: ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(<?= $faq['FAQ_nbr'] ?>)">
                            <?= htmlspecialchars($faq['question_content']) ?>
                        </div>
                        <div id="answer-<?= $faq['FAQ_nbr'] ?>" class="faq-answer">
                            <?= htmlspecialchars($faq['answer_question']) ?>
                            <div class="faq-meta">
                                Added by <?= htmlspecialchars($faq['first_nameT'] . ' ' . $faq['last_nameT']) ?> 
                                on <?= date('M j, Y', strtotime($faq['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleAnswer(faqId) {
            const answer = document.getElementById(`answer-${faqId}`);
            answer.style.display = answer.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
