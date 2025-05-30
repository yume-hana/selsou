document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let calendar;
    let selectedDate = '';
    const refreshIndicator = document.querySelector('.refresh-indicator');
    
    // Modal elements
    const fcModal = document.getElementById('fc-modal');
    const cancelModal = document.getElementById('cancel-modal');
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const cancelMeetingDetails = document.getElementById('cancel-meeting-details');
    
    // Form elements
    const fcForm = document.getElementById('fc-form');
    const eventTitle = document.getElementById('event-title');
    const eventTime = document.getElementById('event-time');
    const eventPlace = document.getElementById('event-place');
    const dateError = document.getElementById('date-error');
    
    // Buttons
    const fcCancel = document.getElementById('fc-cancel');
    const confirmCancel = document.getElementById('confirm-cancel');
    const abortCancel = document.getElementById('abort-cancel');
    
    // Initialize FullCalendar
    initializeCalendar();
    
    // Function to initialize the calendar
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            selectMirror: true,
            navLinks: true,
            editable: false,
            dayMaxEvents: true,
            select: handleDateSelect,
            eventClick: handleEventClick,
            events: 'student_appointments.php', // Load events from the PHP endpoint
            eventDidMount: function(info) {
                // Add a class based on the event status
                if (info.event.extendedProps.status) {


                    info.el.classList.add('event-' + info.event.extendedProps.status);
                }
            }
        });
        
        calendar.render();
    }
    
    // Function to handle date selection for new appointment
    function handleDateSelect(selectInfo) {
        // Prevent booking appointments in the past
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const selectedDateObj = new Date(selectInfo.startStr);
        selectedDateObj.setHours(0, 0, 0, 0);
        
        if (selectedDateObj < today) {
            showTemporaryMessage('Cannot book appointments in the past', 'error');
            return;
        }
        
        // Format the selected date for display
        selectedDate = selectInfo.startStr;
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Display the selected date in the modal
        selectedDateDisplay.textContent = `Date: ${formattedDate}`;
        
        // Show the booking modal
        fcModal.style.display = 'flex';
        
        // Clear any previous form inputs
        fcForm.reset();
        dateError.textContent = '';
    }
    
    // Function to handle clicking on an existing appointment
    function handleEventClick(clickInfo) {
        const event = clickInfo.event;
        const status = event.extendedProps.status;
        
        // Only allow cancellation for pending or accepted meetings
        if (status !== 'pending' && status !== 'accepted') {
            showTemporaryMessage(`This meeting is ${status} and cannot be modified`, 'info');
            return;
        }
        
        // Format the event date and time for display
        const eventDate = new Date(event.start).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const eventTime = new Date(event.start).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Populate the cancel modal with meeting details
        cancelMeetingDetails.innerHTML = `
            <p><strong>Topic:</strong> ${event.title}</p>
            <p><strong>Date:</strong> ${eventDate}</p>
            <p><strong>Time:</strong> ${eventTime}</p>
            <p><strong>Location:</strong> ${event.extendedProps.place || 'Not specified'}</p>
            <p><strong>Status:</strong> <span class="status-${event.extendedProps.status}">${event.extendedProps.status}</span></p>
            <p>Are you sure you want to cancel this meeting?</p>
            <input type="hidden" id="cancel-meeting-date" value="${event.start.toISOString().split('T')[0]}">
            <input type="hidden" id="cancel-meeting-tutor" value="${event.extendedProps.tutor_id}">
        `;
        
        // Show the cancel modal
        cancelModal.style.display = 'flex';
    }
    
    // Event listeners for modal buttons
    if (fcCancel) {
        fcCancel.addEventListener('click', function() {
            fcModal.style.display = 'none';
        });
    }
    
    if (abortCancel) {
        abortCancel.addEventListener('click', function() {
            cancelModal.style.display = 'none';
        });
    }
    
    if (confirmCancel) {
        confirmCancel.addEventListener('click', function() {
            const meetingDate = document.getElementById('cancel-meeting-date').value;
            const tutorId = document.getElementById('cancel-meeting-tutor').value;
            
            // Call API to cancel the meeting
            cancelMeeting(tutorId, meetingDate);
        });
    }
    
    // Form submission for booking new appointment
    if (fcForm) {
        fcForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }
            
            // Create meeting request payload
            const meetingData = {
                registration_nbr: currentUser.registration_nbr,
                Tutor_Id: currentUser.tutor_id,
                Meeting_date: selectedDate,
                Meeting_time: eventTime.value,
                Meeting_location: eventPlace.value,
                content_MT: eventTitle.value,
                state_MT: 'pending'
            };
            
            // Submit the meeting request
            requestMeeting(meetingData);
        });
    }
    
    // Function to validate the form
    function validateForm() {
        let isValid = true;
        
        if (!eventTitle.value.trim()) {
            dateError.textContent = 'Please enter a topic for the meeting';
            isValid = false;
        } else if (!eventTime.value) {
            dateError.textContent = 'Please select a time for the meeting';
            isValid = false;
        } else if (!eventPlace.value.trim()) {
            dateError.textContent = 'Please enter a location for the meeting';
            isValid = false;
        }
        
        return isValid;
    }
    
    // Function to submit meeting request to API
    function requestMeeting(meetingData) {
        fetch('request_meeting.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(meetingData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTemporaryMessage('Meeting request submitted successfully', 'success');
                fcModal.style.display = 'none';
                refreshCalendar();
            } else {
                throw new Error(data.error || 'Failed to create meeting');
            }
        })
  .catch(error => {
    console.error("Error submitting meeting:", error.message);
    alert("Error: " + error.message); // طبع مؤقت
});

    }
    
    // Function to cancel a meeting
    function cancelMeeting(tutorId, meetingDate) {
        const formData = new FormData();
        formData.append('cancel_meeting', '1');
        formData.append('Tutor_ID', tutorId);
        formData.append('Meeting_date', meetingDate);
        
        fetch('student_appointments.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTemporaryMessage('Meeting canceled successfully', 'success');
                cancelModal.style.display = 'none';
                refreshCalendar();
            } else {
                throw new Error(data.error || 'Failed to cancel meeting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showTemporaryMessage('An error occurred. Please try again.', 'error');
        });
    }
    
    // Function to refresh the calendar
    function refreshCalendar() {
        calendar.refetchEvents();
        showRefreshIndicator();
    }
    
    // Function to show the refresh indicator temporarily
    function showRefreshIndicator() {
        refreshIndicator.classList.add('active');
        setTimeout(() => {
            refreshIndicator.classList.remove('active');
        }, 3000);
    }
    
    // Function to show temporary message
    function showTemporaryMessage(message, type) {
        const messageElement = document.createElement('div');
        messageElement.className = `message-popup ${type}`;
        messageElement.textContent = message;
        
        document.body.appendChild(messageElement);
        
        setTimeout(() => {
            messageElement.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            messageElement.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(messageElement);
            }, 300);
        }, 3000);
    }
    
    // Add window click events to close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === fcModal) {
            fcModal.style.display = 'none';
        }
        if (event.target === cancelModal) {
            cancelModal.style.display = 'none';
        }
    });
    
    // Check if the session includes user data, otherwise show a message
    if (!currentUser.registration_nbr || !currentUser.tutor_id) {
        showTemporaryMessage('Please log in to book appointments', 'warning');
    }
});