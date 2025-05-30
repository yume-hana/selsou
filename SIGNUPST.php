<?php
session_start();
$errors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear the session data after retrieving it
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="SIGNUPST.css">
    <title>Lead My Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        .container {
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .signup-box {
            transition: opacity 0.5s ease-out;
        }
        .signup-box.fade-out {
            opacity: 0;
        }
    </style>
    <script>
        function validateEmail(email) {
            // Much stricter email validation
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            // Additional checks
            if (!emailRegex.test(email)) return false;
            
            // Check domain length
            const parts = email.split('@');
            if (parts.length !== 2) return false;
            
            const domain = parts[1];
            const domainParts = domain.split('.');
            if (domainParts.length < 2) return false;
            
            // Check TLD
            const tld = domainParts[domainParts.length - 1].toLowerCase();
            if (tld !== 'com' && tld !== 'org' && tld !== 'net' && tld !== 'edu') {
                return false;
            }
            
            // Check domain name length
            const domainName = domainParts[0];
            if (domainName.length < 2) return false;
            
            return true;
        }

        function validateEmailField(input) {
            const email = input.value.trim();
            
            if (email === '') {
                input.setCustomValidity('Email is required.');
                return false;
            } else if (!validateEmail(email)) {
                input.setCustomValidity('Please enter a valid email address (e.g., name@domain.com)');
                return false;
            } else {
                input.setCustomValidity('');
                return true;
            }
        }

        // Add event listener when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            const signInLink = document.querySelector('.sign-in');
            if (signInLink) {
                signInLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const signInSignUp = document.querySelector('.signInSignUp');
                    const formSection = document.querySelector('.form-section');
                    
                    // Add slide animation classes
                    signInSignUp.style.transform = 'translateX(100%)';
                    signInSignUp.style.opacity = '0';
                    formSection.style.transform = 'translateX(100%)';
                    formSection.style.opacity = '0';
                    
                    // Navigate after animation
                    setTimeout(() => {
                        window.location.href = 'animationSignIn.html';
                    }, 500);
                });
            }
        });
    </script>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-left">
          <img src="logo.png" alt="Logo" class="site-logo">
          <h1 class="site-name">
             <div class="logo">Lead My Way</div>
          </h1>
        </div>
    </nav>

    <div class="container">
        <div class="signup-box">
            <div class="signInSignUp">
                <div class="welcomeSign">
                    <h1>WELCOME! <br></h1>
                    <div class="tokeep">
                        <h3>
                            To keep connected with us please
                            Sign in with your personal info</h3>
                        <a href="animationSignIn.html" class="sign-in">Sign In</a>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Create Student's Account</h2>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message" style="color: green; margin-bottom: 20px; padding: 10px 20px; background-color: rgba(93, 255, 206, 0.1); border-radius: 8px; text-align: center;">
                        <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="student_register.php" method="post" id="registrationForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" name="ST_first_name" required minlength="2" maxlength="30"
                                placeholder="Enter your first name" 
                                value="<?php echo isset($form_data['ST_first_name']) ? htmlspecialchars($form_data['ST_first_name']) : ''; ?>">
                            <?php if (isset($errors['ST_first_name'])): ?>
                                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_first_name']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" name="ST_last_name" required minlength="2" maxlength="30"
                                placeholder="Enter your last name" 
                                value="<?php echo isset($form_data['ST_last_name']) ? htmlspecialchars($form_data['ST_last_name']) : ''; ?>">
                            <?php if (isset($errors['ST_last_name'])): ?>
                                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_last_name']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="registration">Registration Number</label>
                        <input type="text" id="registration" name="registration_nbr" required
                            placeholder="Enter your Registration Number" 
                            value="<?php echo isset($form_data['registration_nbr']) ? htmlspecialchars($form_data['registration_nbr']) : ''; ?>">
                        <?php if (isset($errors['registration_nbr'])): ?>
                            <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['registration_nbr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="ST_email_address" required 
                                placeholder="eg: email@gmail.com"
                                oninput="validateEmailField(this)"
                                onblur="validateEmailField(this)"
                                value="<?php echo isset($form_data['ST_email_address']) ? htmlspecialchars($form_data['ST_email_address']) : ''; ?>">
                            <?php if (isset($errors['ST_email_address'])): ?>
                                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_email_address']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="ST_password" required minlength="8" maxlength="20"
                                placeholder="Write a complex password">
                            <?php if (isset($errors['ST_password'])): ?>
                                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_password']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="ST_address" required 
                            placeholder="Enter your address"
                            value="<?php echo isset($form_data['ST_address']) ? htmlspecialchars($form_data['ST_address']) : ''; ?>">
                        <?php if (isset($errors['ST_address'])): ?>
                            <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_address']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="date_of_birth" max="2008-12-31" required
                                value="<?php echo isset($form_data['date_of_birth']) ? htmlspecialchars($form_data['date_of_birth']) : ''; ?>">
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['date_of_birth']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select name="ST_gender" id="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo (isset($form_data['ST_gender']) && $form_data['ST_gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (isset($form_data['ST_gender']) && $form_data['ST_gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                            <?php if (isset($errors['ST_gender'])): ?>
                                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_gender']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="ST_Nationality" required
                            placeholder="Enter your nationality" 
                            value="<?php echo isset($form_data['ST_Nationality']) ? htmlspecialchars($form_data['ST_Nationality']) : ''; ?>">
                        <?php if (isset($errors['ST_Nationality'])): ?>
                            <span class="error" style="color: red; font-size: 0.8em;"><?php echo $errors['ST_Nationality']; ?></span>
                        <?php endif; ?>
                    </div>

                    <input type="submit" value="sign up" class="sign-up">
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>by SHINCODE</p>
    </footer>
</body>
</html>