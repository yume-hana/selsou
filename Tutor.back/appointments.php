<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

// Status transition matrix
$allowedTransitions = [
    'pending' => ['accepted', 'rejected', 'rescheduled'],
    'accepted' => ['canceled', 'completed', 'missed', 'rescheduled'],
    'rescheduled' => ['canceled', 'completed', 'missed'],
    'rejected' => [],
    'canceled' => [],
    'completed' => [],
    'missed' => []
];

try {
    // Get all meetings
    $stmt = $pdo->prepare("
        SELECT m.*, s.ST_first_name, s.ST_last_name 
        FROM meeting m
        JOIN student s ON m.Registration_nbr = s.registration_nbr
        WHERE m.Tutor_ID = :tutor_id
        ORDER BY m.Meeting_date DESC
    ");
    $stmt->execute([':tutor_id' => $_SESSION['tutor_id']]);
    $appointments = $stmt->fetchAll();

    // Handle meeting updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $meetingId = $_POST['meeting_id'];
        $studentId = $_POST['student_id'];
        $newStatus = $_POST['status'];
        $currentStatus = $_POST['current_status'];
        
        // Validate transition
        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            throw new Exception("Invalid status transition");
        }

        $updateData = [
            ':status' => $newStatus,
            ':tutor_id' => $_SESSION['tutor_id'],
            ':student_id' => $studentId,
            ':meeting_id' => $meetingId
        ];

        // Handle different update types
        $sql = "UPDATE meeting SET status = :status";
        
        if ($newStatus === 'rescheduled') {
            $sql .= ", Meeting_date = :date, Meeting_time = :time, Meeting_location = :location";
            $updateData[':date'] = $_POST['meeting_date'];
            $updateData[':time'] = $_POST['meeting_time'];
            $updateData[':location'] = $_POST['location'];
        }
        
        if ($newStatus === 'completed' || $newStatus === 'missed') {
            $sql .= ", content_MT = :notes";
            $updateData[':notes'] = $_POST['notes'];
        }

        $sql .= " WHERE Meeting_ID = :meeting_id 
                AND Tutor_ID = :tutor_id 
                AND Registration_nbr = :student_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateData);

        header("Location: appointments.php?success=1");
        exit();
    }
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch(Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .status-pending { background-color: #fff3cd; }
        .status-accepted { background-color: #d4edda; }
        .status-rescheduled { background-color: #cce5ff; }
        .status-canceled { background-color: #f8d7da; }
        .status-completed { background-color: #d1ecf1; }
        .status-missed { background-color: #f5b7b1; }
        
        .action-form {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Appointment Management</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Update successful!</div>
        <?php endif; ?>

        <?php if (!empty($appointments)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $apt): ?>
                    <tr class="status-<?= htmlspecialchars($apt['status']) ?>">
                        <td><?= htmlspecialchars($apt['ST_first_name'] . ' ' . $apt['ST_last_name']) ?></td>
                        <td><?= htmlspecialchars($apt['Meeting_date']) ?></td>
                        <td><?= htmlspecialchars($apt['Meeting_time']) ?></td>
                        <td><?= htmlspecialchars($apt['Meeting_location']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($apt['status'])) ?></td>
                        <td>
                            <form class="action-form" method="POST">
                                <input type="hidden" name="meeting_id" value="<?= $apt['Meeting_ID'] ?>">
                                <input type="hidden" name="student_id" value="<?= $apt['Registration_nbr'] ?>">
                                <input type="hidden" name="current_status" value="<?= $apt['status'] ?>">

                                <?php if ($apt['status'] === 'pending'): ?>
                                    <div class="form-group">
                                        <label>Action:</label>
                                        <select name="status" required>
                                            <option value="accepted">Accept</option>
                                            <option value="rejected">Reject</option>
                                            <option value="rescheduled">Reschedule</option>
                                        </select>
                                    </div>

                                    <?php if ($apt['requested_by'] === 'student'): ?>
                                        <div class="form-group reschedule-fields">
                                            <label>New Date:</label>
                                            <input type="date" name="meeting_date" value="<?= $apt['Meeting_date'] ?>" required>
                                            
                                            <label>New Time:</label>
                                            <input type="time" name="meeting_time" value="<?= $apt['Meeting_time'] ?>" required>
                                            
                                            <label>Location:</label>
                                            <input type="text" name="location" value="<?= $apt['Meeting_location'] ?>" required>
                                        </div>
                                    <?php endif; ?>

                                <?php elseif (in_array($apt['status'], ['accepted', 'rescheduled'])): ?>
                                    <div class="form-group">
                                        <label>Action:</label>
                                        <select name="status" required>
                                            <option value="canceled">Cancel</option>
                                            <option value="rescheduled">Reschedule</option>
                                            <?php if (strtotime($apt['Meeting_date'] . ' ' . $apt['Meeting_time']) < time()): ?>
                                                <option value="completed">Mark as Completed</option>
                                                <option value="missed">Mark as Missed</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="form-group reschedule-fields">
                                        <label>New Date:</label>
                                        <input type="date" name="meeting_date" value="<?= $apt['Meeting_date'] ?>" required>
                                        
                                        <label>New Time:</label>
                                        <input type="time" name="meeting_time" value="<?= $apt['Meeting_time'] ?>" required>
                                        
                                        <label>Location:</label>
                                        <input type="text" name="location" value="<?= $apt['Meeting_location'] ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Notes:</label>
                                        <textarea name="notes"><?= htmlspecialchars($apt['content_MT']) ?></textarea>
                                    </div>

                                <?php elseif ($apt['status'] === 'rejected'): ?>
                                    <p>This meeting was rejected</p>
                                
                                <?php elseif ($apt['status'] === 'canceled'): ?>
                                    <p>This meeting was canceled</p>
                                
                                <?php else: ?>
                                    <p>No actions available</p>
                                <?php endif; ?>

                                <button type="submit" class="btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>
    <script>
        // Show/hide reschedule fields based on selection
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function() {
                const rescheduleFields = this.closest('form').querySelector('.reschedule-fields');
                if (rescheduleFields) {
                    rescheduleFields.style.display = this.value === 'rescheduled' ? 'block' : 'none';
                }
            });
        });
    </script>
</body>
</html>
