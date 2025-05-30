
<?php
session_start();
require_once 'db.php'; // نحتاج الاتصال بقاعدة البيانات

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="meetingST.css">
    <title>Lead My Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <nav class="navbar">
            <div class="logo">Lead My Way</div>
            <div class="nav-links">
                <a href="studentFAQ.php" class="nav-link">FAQ</a>
                <a href="studenthome.php" class="nav-link">Home</a>
                <a href="QST-STUDENT.html" class="nav-link">Question</a>
                <a href="meetingST.php" class="nav-link active">Meeting</a>
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
                <a href="profile.html" class="username">Guest</a>
                <div class="user-profile">
                    <img src="use.jpg" alt="User profile" />
                </div>
            </div>
        </nav>
        <div class="page">
            <div class="calendar-container">
                <!-- Calendar legend -->
                <div class="calendar-legend">
                    <h3>Meeting Status</h3>
                    <div class="legend-item">
                        <div class="color-box pending"></div>
                        <span>Pending</span>
                    </div>
                    <div class="legend-item">
                        <div class="color-box accepted"></div>
                        <span>Accepted</span>
                    </div>
                    <div class="legend-item">
                        <div class="color-box rejected"></div>
                        <span>Rejected</span>
                    </div>
                    <div class="legend-item">
                        <div class="color-box rescheduled"></div>
                        <span>Rescheduled</span>
                    </div>
                    <!-- New status items -->
                    <div class="legend-item">
                        <div class="color-box canceled"></div>
                        <span>Canceled</span>
                    </div>
                    <div class="legend-item">
                        <div class="color-box completed"></div>
                        <span>Completed</span>
                    </div>
                </div>
                <!-- Calendar -->
                <div id="calendar" class="calendar"></div>
            </div>

            <!-- Refresh indicator -->
            <div class="refresh-indicator">Meeting data updated</div>
        </div>

        <!-- Modal for Booking -->
        <div id="fc-modal" class="fc-modal">
            <div class="fc-modal-content">
                <h3>Request an Appointment</h3>
                <p id="selected-date-display"></p>
                <form id="fc-form" method="post" action="request_meeting.php">
                    <label>Topic of the Meeting:
                        <input type="text" id="event-title" required name="MEET-Topic"
                            placeholder=" Enter Topic of the Meeting">
                    </label>
                    <label>Meeting Time:
                        <input type="time" id="event-time" required name="MEET-Time">
                    </label>
                    <label>Meeting Place:
                        <input type="text" id="event-place" placeholder="Enter meeting location" required
                            name="MEET-location">
                    </label>
                    <div class="error-message" id="date-error"></div>
                    <button type="submit" id="fc-submit">Request</button>
                    <button type="reset" id="fc-reset">Reset</button>
                    <button type="button" id="fc-cancel">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Modal for Meeting Details/Cancellation -->
        <div id="cancel-modal" class="fc-modal">
            <div class="fc-modal-content">
                <h3>Meeting Details</h3>
                <div class="meeting-details" id="cancel-meeting-details"></div>
                <div class="button-group">
                    <button type="button" id="confirm-cancel">Yes, Cancel</button>
                    <button type="button" id="abort-cancel">No, Keep It</button>
                </div>
            </div>
        </div>

        <footer>
            <p> by SHINCODE</p>
        </footer>
    </div><script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
    const currentUser = {
        registration_nbr: "<?php echo $_SESSION['registration_nbr'] ?? ''; ?>",
        tutor_id: "<?php echo $_SESSION['tutor_id'] ?? ''; ?>"
    };
</script>




    <!-- FullCalendar JS -->
    
    <script src="meetingST.js"></script>
</body>

</html>