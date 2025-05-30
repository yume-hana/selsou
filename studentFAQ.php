<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // ماشي ob_end_clean()

error_log("FAQ page - Session contents: " . print_r($_SESSION, true));

// التحقق من الجلسة
if (!isset($_SESSION['registration_nbr']) || $_SESSION['user_type'] !== 'student') {
    error_log("FAQ page - Invalid session, redirecting to login");
    header("Location: login.php");
    exit;
}

include("auth.php");
include("db.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="studentFAQ.css">
    <title>Lead My Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">

    <script src="studentFAQ.js" defer></script>
</head>

<body>
    <div class="container">
        <nav class="navbar">
            <div class="logo">Lead My Way</div>
            <div class="nav-links">
                <a href="#" class="nav-link active">FAQ</a>
                <a href="studenthome.php" class="nav-link">Home</a>
                <a href="QST-STUDENT.html" class="nav-link">Question</a>
                <a href="meetingST.php" class="nav-link">Meeting</a>
                <a href="#" class="nav-link">My Tutor</a>
                <div class="notification-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
            </div>
            <div class="user-section">
                <div class="username">Guest </div>

                <div class="user-profile">
                    <img src="use.jpg" alt="User profile" />
                </div>
            </div>
        </nav>
        <div class="page">
            <h1 id="tesxtFAQ">Frequently Asked Questions </h1>

            <?php
            // 1) Verify "student"
          
            // 2) Load all FAQs
            $sql = "
  SELECT f.FAQ_nbr, f.question_content, f.answer_question,
         CONCAT(t.first_nameT,' ',t.last_nameT) AS tutor_fullname
  FROM quesfaq f
  JOIN tutor t ON f.Tutor_ID = t.Tutor_ID
  ORDER BY f.FAQ_nbr DESC
";
            $result = mysqli_query($conn, $sql);

            // 3) Log consults
            $registration_nbr = $_SESSION['registration_nbr'];
            while ($row = mysqli_fetch_assoc($result)) {
                $faq_id = $row['FAQ_nbr'];
                $check = mysqli_query(
                    $conn,
                    "SELECT 1 FROM consult
                    WHERE FAQ_nbr='$faq_id'
                    AND registration_nbr='$registration_nbr'"
                );
                if (mysqli_num_rows($check) === 0) {
                    $now_date = date("Y-m-d");
                    $now_time = date("H:i:s");
                    mysqli_query(
                        $conn,
                        "INSERT INTO consult
                      (FAQ_nbr, registration_nbr, date_consult, time_consult)
                      VALUES ('$faq_id','$registration_nbr','$now_date','$now_time')"
                                   );
                }
            }
            // Re-execute the FAQ query so $result has data again
            $result = mysqli_query($conn, $sql);
            ?>


            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" class="search-input" placeholder="Search for questions...">
            </div>

            <div class="faq-section">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <?= htmlspecialchars($row['question_content']) ?>
                            <div class="faq-toggle">
                                <!-- SVG plus icon -->
                                <svg …>…</svg>
                            </div>
                        </div>
                        <div class="faq-answer">
                        <pre><?= $row['answer_question'] ?></pre>

                            
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>


        </div>
    </div>
    <footer>
        <p> by SHINCODE</p>
    </footer>
 
</body>

</html>