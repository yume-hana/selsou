<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

try {
    // Get tutor profile
    $tutorStmt = $pdo->prepare("
        SELECT first_nameT, last_nameT, Email_addressT 
        FROM tutor 
        WHERE Tutor_ID = ?
    ");
    $tutorStmt->execute([$_SESSION['tutor_id']]);
    $tutor = $tutorStmt->fetch();

    // Get upcoming meetings
    $meetingsStmt = $pdo->prepare("
        SELECT m.Meeting_date, m.Meeting_time, s.ST_first_name, s.ST_last_name 
        FROM meeting m
        JOIN student s ON m.Registration_nbr = s.registration_nbr
        WHERE m.Tutor_ID = ? 
        AND m.Meeting_date >= CURDATE()
        ORDER BY m.Meeting_date ASC
        LIMIT 5
    ");
    $meetingsStmt->execute([$_SESSION['tutor_id']]);
    
    // Get pending questions
    $questionsStmt = $pdo->prepare("
        SELECT COUNT(*) AS pending_count 
        FROM message_quest mq
        JOIN ask a ON mq.quest_nbr = a.quest_nbr
        JOIN student s ON a.Registration_nbr = s.registration_nbr
        WHERE s.Tutor_ID = ? AND mq.state != 'answered'
    ");
    $questionsStmt->execute([$_SESSION['tutor_id']]);
    $pendingQuestions = $questionsStmt->fetchColumn();

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Welcome, <?= htmlspecialchars($tutor['first_nameT']) ?>!</h1>
            <nav>
                <a href="appointments.php">Appointments</a>
                <a href="questions.php">Questions (<?= $pendingQuestions ?>)</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <section class="quick-stats">
            <div class="stat-card">
                <h3>Upcoming Meetings</h3>
                <?php if ($meetingsStmt->rowCount() > 0): ?>
                    <ul class="meetings-list">
                        <?php while ($meeting = $meetingsStmt->fetch()): ?>
                        <li>
                            <div class="meeting-time">
                                <?= date('M j', strtotime($meeting['Meeting_date'])) ?>
                                <?= date('g:i a', strtotime($meeting['Meeting_time'])) ?>
                            </div>
                            <div class="student-name">
                                <?= htmlspecialchars($meeting['ST_first_name'] . ' ' . $meeting['ST_last_name']) ?>
                            </div>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-data">No upcoming meetings</p>
                <?php endif; ?>
                <a href="appointments.php" class="see-all">View All →</a>
            </div>

            <div class="stat-card">
                <h3>Recent Activity</h3>
                <!-- Add recent activity feed here -->
                <p class="no-data">No recent activity</p>
            </div>
        </section>
    </div>
</body>
</html>