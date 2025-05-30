// This is the complete login script for animationSignIn.js
document.addEventListener("DOMContentLoaded", function () {
    console.log("Document loaded, attaching event listeners");
    
    // Get the form element - use a more generic selector to make sure we find it
    const loginForm = document.querySelector("form");
    
    if (loginForm) {
        console.log("Login form found, attaching submit event");
        
        // Attach to form submit event rather than button clicku
        loginForm.addEventListener("submit", function(event) {
            event.preventDefault();
            console.log("Form submitted");
            
            // Get email and password fields - use more flexible selectors
            const emailField = document.querySelector('input[type="email"], input[name*="ST_Email_address"]');
            const passwordField = document.querySelector('input[type="password"], input[name*="ST_password"]');
            
            if (!emailField || !passwordField) {
                console.error("Could not find email or password fields");
                return;
            }
            
            const email = emailField.value.trim();
            const password = passwordField.value;
            
            console.log("Validating email:", email);
            
            // Validate inputs
            if (!email) {
                showError("Please enter your email address");
                return;
            }
            
            if (!validateEmail(email)) {
                showError("Please enter a valid email address");
                return;
            }
            
            if (!password) {
                showError("Please enter your password");
                return;
            }
            
            // Show loading state
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : "Sign In";
            if (submitButton) {
                submitButton.innerHTML = "Signing in...";
                submitButton.disabled = true;
            }
            
            console.log("Sending login request");
            
            // Create form data for submission
            const formData = new FormData();
            formData.append(emailField.name, email);
            formData.append(passwordField.name, password);
            
            // Remember me checkbox
            const rememberCheckbox = document.querySelector('input[type="checkbox"]');
            if (rememberCheckbox && rememberCheckbox.checked) {
                formData.append(rememberCheckbox.name, "on");
            }
            
            // Send data to server - use the actual endpoint from your HTML
            fetch("login_api.php", {
                method: "POST",
                body: formData
            })
            .then(response => {
                console.log("Received response", response);
                if (!response.ok) {
                    throw new Error(`Server responded with status ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Login response:", data);
                if (data.success) {
                    // Login successful
                    showSuccess(data.message || "Login successful!");
                    
                    // Redirect to student FAQ page
                    setTimeout(() => {
                        window.location.href = "studentFAQ.php";
                    }, 1000);
                } else {
                    // Login failed
                    showError(data.message || "Invalid email or password");
                    if (submitButton) {
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error("Login error:", error);
                showError("An error occurred during login. Please try again.");
                if (submitButton) {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            });
        });
    } else {
        console.error("Login form not found");
    }
    
    // Set up password toggle if it's not already handled in HTML
    setupPasswordToggle();
});



// Helper function to display errors
function showError(message) {
    console.error("Error:", message);
    const errorElement = document.getElementById("error-message");
    if (errorElement) {
        errorElement.style.color = "rgb(195, 18, 18)";
        errorElement.textContent = message;
    } else {
        // If no error element exists, create an alert
        alert(message);
    }
}

// Helper function to display success messages
function showSuccess(message) {
    console.log("Success:", message);
    const errorElement = document.getElementById("error-message");
    if (errorElement) {
        errorElement.style.color = "green";
        errorElement.textContent = message;
    }
}

// Email validation function
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Setup password toggle if not already handled in HTML
function setupPasswordToggle() {
    // Check if toggle is already set up
    const existingToggle = document.querySelector('.password-toggle');
    const passwordInput = document.querySelector('input[type="password"]');
    
    if (passwordInput && !existingToggle._hasEventListener) {
        const togglePassword = document.getElementById('togglePassword');
        
        if (togglePassword) {
            console.log("Setting up password toggle");
            togglePassword._hasEventListener = true;
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                
                // Toggle eye icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    }
}