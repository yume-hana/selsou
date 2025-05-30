<?php
// Include the database connection
include('db.php');

// Fetch data for the student
$student_id = $_SESSION['registration_nbr']; // Assuming session holds the student ID

// Query to fetch the next meeting details
$queryNextMeeting = "SELECT * FROM meeting WHERE registration_nbr = '$student_id' AND Meeting_date >= CURDATE() ORDER BY Meeting_date LIMIT 1";
$resultNextMeeting = mysqli_query($conn, $queryNextMeeting);
$nextMeeting = mysqli_fetch_assoc($resultNextMeeting);

// Query to fetch the last question asked by the student
$queryLastQuestion = "SELECT * FROM message_quest WHERE registration_nbr = '$student_id' ORDER BY quest_date DESC LIMIT 1";
$resultLastQuestion = mysqli_query($conn, $queryLastQuestion);
$lastQuestion = mysqli_fetch_assoc($resultLastQuestion);

// Query to count the number of questions asked by the student
$queryQCount = "SELECT COUNT(*) as total_questions FROM message_quest WHERE registration_nbr = '$student_id'";
$resultQCount = mysqli_query($conn, $queryQCount);
$qCount = mysqli_fetch_assoc($resultQCount)['total_questions'];

// Query to count the number of answered questions
$queryAnsweredCount = "SELECT COUNT(*) as answered_count FROM message_quest WHERE registration_nbr = '$student_id' AND state= 'answered'";
$resultAnsweredCount = mysqli_query($conn, $queryAnsweredCount);
$answeredCount = mysqli_fetch_assoc($resultAnsweredCount);

// Query to count the number of appointments
$queryMCount = "SELECT COUNT(*) as total_appointments FROM meeting WHERE registration_nbr = '$student_id'";
$resultMCount = mysqli_query($conn, $queryMCount);
$mCount = mysqli_fetch_assoc($resultMCount)['total_appointments'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="student-hometest1.css">
    <title>Lead My Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="logo">Lead My Way</div>
        <div class="nav-links">
            <a href="studentFAQ.php" class="nav-link">FAQ</a>
            <a href="studenthome.php" class="nav-link active">Home</a>
            <a href="QST-STUDENT.html" class="nav-link">Question</a>
            <a href="meetingST.php" class="nav-link">Meeting</a>
            <a href="#" class="nav-link">My Tutor</a>
        </div>
        <div class="user-section">
            <div class="username"><?php echo htmlspecialchars($student_name); ?></div>
            <div class="user-profile">
                <img src="https://via.placeholder.com/40" alt="User profile" />
            </div>
        </div>
    </nav>

    <div class="page">
        <div class="dashboard">
            <h1 class="dashboard-title">✨ Student Overview Dashboard</h1>
            <div class="dashboard-content">
                <div class="welcome-section">
                    <h2>From confusion to clarity, your journey starts here.🚀</h2>
                </div>
                <div class="stats-section">
                    <div class="stat-card">
                        <h3>Questions Asked</h3>
                        <p class="stat-number"><?php echo $qCount; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Appointments</h3>
                        <p class="stat-number"><?php echo $mCount; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Answered Questions</h3>
                        <p class="stat-number"><?php echo $answeredCount['answered_count']; ?></p>
                    </div>
                </div>
                <div class="upcoming-section">
                    <h3>Upcoming Appointment 🕒</h3>
                    <p>
                        <?php
                        if ($nextMeeting) {
                            echo "📅 " . $nextMeeting['Meeting_date'] . " at " . $nextMeeting['Meeting_time'] . " — " . $nextMeeting['Meeting_location'];
                        } else {
                            echo "No upcoming appointments";
                        }
                        ?>
                    </p>
                </div>
                <div class="last-question-section">
                    <h3>Last Asked Question 📝</h3>
                    <p><?php echo $lastQuestion ? htmlspecialchars($lastQuestion['question_content']) : "No questions asked yet"; ?></p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p> by SHINCODE</p>
    </footer>
</body>

</html>