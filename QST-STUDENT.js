// Global variables
let questions = [];
let currentFilter = 'all';

// 1. Fetch questions from the server
function fetchQuestions() {
    $.ajax({
        url: 'question_api.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            try {
                const data = response;
                 if (data.status === 'success') {
                    questions = data.data;
                    renderQuestions();
                } else {
                    console.error("Error fetching questions:", data.message);
                }
            } catch(e) {
                console.error("Error parsing data:", e);
                console.log("Raw response:", response);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.log("Response text:", xhr.responseText);
        }
    });
}

// 2. Render questions to the page
function renderQuestions() {
    const questionList = document.getElementById('question-list');
    questionList.innerHTML = '';

    let filtered = questions;
    if (currentFilter === 'answered') {
        filtered = questions.filter(q => q.status === 'answered');
    } else if (currentFilter === 'unanswered') {
        filtered = questions.filter(q => q.status === 'pending');
    }

    if (filtered.length === 0) {
        questionList.innerHTML = '<p class="no-questions">No questions found for the selected filter.</p>';
        return;
    }

    filtered.forEach(q => {
        const item = document.createElement('div');
        item.classList.add('question-item');


        const header = document.createElement('div');
        header.className = 'question-header';
        header.innerHTML = `
            <span class="question-number">Question #${q.number}</span>
            <span class="question-date">${q.date} ${q.time}</span>
        `;

        const content = document.createElement('div');
        content.className = 'question-content';
        content.textContent = q.content;

        const status = document.createElement('div');
        status.className = `question-status status-${q.status}`;
        status.textContent = q.status === 'answered' ? 'Answered' : 'Pending';

        item.appendChild(header);
        item.appendChild(content);
        item.appendChild(status);

        // Add answer section if question has been answered
        if (q.status === 'answered' && q.answer) {
            const answerSection = document.createElement('div');
            answerSection.className = 'answer-section';

            const answerHeader = document.createElement('div');
            answerHeader.className = 'answer-header';
            answerHeader.textContent = 'Tutor\'s Answer:';

            const answerContent = document.createElement('div');
            answerContent.className = 'answer-content';
            answerContent.textContent = q.answer;

            answerSection.appendChild(answerHeader);
            answerSection.appendChild(answerContent);
            item.appendChild(answerSection);
        }

        questionList.appendChild(item);
    });
}

// 3. Add a new question
function addQuestion(questionText) {
    $.ajax({
        url: 'question_api.php',
        type: 'POST',
        data: {
            action: 'add_question',
            contentQ: questionText,
            token: $('input[name="token"]').val() 
        },
        success: function(response) {
            console.log("Raw response: ", response);
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.status === 'success') {
                    fetchQuestions(); // Refresh the question list
                    // Show success message to user
                    alert("Your question has been sent successfully!");
                } else {
                    alert("Error: " + data.message);
                }
            } catch(e) {
                console.error("Error parsing data:", e);
                console.log("Raw response:", response);
                alert("An error occurred while processing the response");
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.log("Response text:", xhr.responseText);
            alert("Failed to connect to server. Please try again.");
        }
    });
}

// 4. Setup filter buttons
function setupFilterButtons() {
    document.querySelectorAll('.div-buttons button').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.div-buttons button').forEach(b => {
                b.classList.remove('active-filter');
            });
            
            // Add active class to clicked button
            this.classList.add('active-filter');
            
            // Update current filter
            currentFilter = this.getAttribute('data-filter');
            
            // Fetch questions with new filter
            fetchQuestions();
        });
    });
}

// 5. Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if CSS is loaded properly
    console.log("DOM loaded, checking CSS...");
    const stylesheets = document.styleSheets;
    console.log(`${stylesheets.length} stylesheets loaded`);
    
    // Initialize the app
    fetchQuestions();
    setupFilterButtons();
    
    // Set up form submission
    document.getElementById('question-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('question-input');
        const content = input.value.trim();
        
        if (!content || content.length < 5) {
            alert("Please enter a question with at least 5 characters.");
            return;
        }
        
        addQuestion(content);
        input.value = '';
    });
    
    // Reset button functionality
    document.getElementById('Reset').addEventListener('click', function() {
        document.getElementById('question-input').value = '';
    });
});