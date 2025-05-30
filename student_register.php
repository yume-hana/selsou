<?php
session_start();
include("db.php");

$errors = [];
$first_name = $last_name = $registration_nbr = $email = $address = $dob = $gender = $nationality = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استرجاع القيم
    $first_name = trim($_POST["ST_first_name"]);
    $last_name = trim($_POST["ST_last_name"]);
    $registration_nbr = trim($_POST["registration_nbr"]);
    $email = trim($_POST["ST_email_address"]);
    $password = $_POST["ST_password"];
    $address = trim($_POST["ST_address"]);
    $dob = $_POST["date_of_birth"];
    $gender = $_POST["ST_gender"];
    $nationality = trim($_POST["ST_Nationality"]);

    // ✅ التحققات
    if (empty($first_name) || strlen($first_name) < 2 || strlen($first_name) > 30) {
        $errors['ST_first_name'] = "First name must be between 2 and 30 characters.";
    }

    if (empty($last_name) || strlen($last_name) < 2 || strlen($last_name) > 30) {
        $errors['ST_last_name'] = "Last name must be between 2 and 30 characters.";
    }

    if (empty($registration_nbr)) {
        $errors['registration_nbr'] = "Registration number is required.";
    } else {
        // Validate registration number format
        if (!is_numeric($registration_nbr)) {
            $errors['registration_nbr'] = "Registration number must contain only numbers.";
        } else {
            // Convert to integer and check range
            $reg_num = intval($registration_nbr);
            if ($reg_num <= 0 || $reg_num > 999999) {
                $errors['registration_nbr'] = "Registration number must be between 1 and 999999.";
            }
        }
    }

    if (strlen($password) < 8 || strlen($password) > 20 || 
        !preg_match("/[0-9]/", $password) || !preg_match("/[a-zA-Z]/", $password)) {
        $errors['ST_password'] = "Password must be 8–20 characters long and contain both letters and numbers.";
    }

    if (empty($dob)) {
        $errors['date_of_birth'] = "Date of birth is required.";
    } else {
        $birth_date = new DateTime($dob);
        $age = (new DateTime())->diff($birth_date)->y;
        if ($age < 17) {
            $errors['date_of_birth'] = "You must be at least 17 years old.";
        }
    }

    if (strlen($address) > 200) {
        $errors['ST_address'] = "Address must not exceed 200 characters.";
    }

    // Enhanced email validation
    if (empty($email)) {
        $errors['ST_email_address'] = "Email is required.";
    } else {
        // Check email format with stricter validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['ST_email_address'] = "Invalid email format.";
        } else {
            // Additional validation checks
            $parts = explode('@', $email);
            if (count($parts) !== 2) {
                $errors['ST_email_address'] = "Invalid email format.";
            } else {
                $domain = $parts[1];
                $domainParts = explode('.', $domain);
                if (count($domainParts) < 2) {
                    $errors['ST_email_address'] = "Invalid email format.";
                } else {
                    $tld = strtolower(end($domainParts));
                    if (!in_array($tld, ['com', 'org', 'net', 'edu'])) {
                        $errors['ST_email_address'] = "Please use a valid email domain (.com, .org, .net, or .edu)";
                    } else {
                        $domainName = $domainParts[0];
                        if (strlen($domainName) < 2) {
                            $errors['ST_email_address'] = "Invalid email format.";
                        } else {
                            // Check if email already exists in database
                            $stmt = $conn->prepare("SELECT ST_email_address FROM student WHERE ST_email_address = ?");
                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $stmt->store_result();
                            if ($stmt->num_rows > 0) {
                                $errors['ST_email_address'] = "This email is already registered.";
                            }
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }

    if (empty($gender) || !in_array($gender, ['male', 'female'])) {
        $errors['ST_gender'] = "Please select a valid gender.";
    }

    if (empty($nationality)) {
        $errors['ST_Nationality'] = "Nationality is required.";
    }

    // Check if registration number already exists
    $stmt = $conn->prepare("SELECT registration_nbr FROM student WHERE registration_nbr = ?");
    $stmt->bind_param("s", $registration_nbr);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors['registration_nbr'] = "Registration number already exists.";
    }
    $stmt->close();

    // ✅ إذا لا توجد أخطاء
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $status = 'pending';

        $stmt = $conn->prepare("INSERT INTO student (registration_nbr, ST_first_name, ST_last_name, ST_email_address, ST_password, ST_address, date_of_birth, ST_gender, ST_Nationality, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $registration_nbr, $first_name, $last_name, $email, $hashed_password, $address, $dob, $gender, $nationality, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Your registration request has been submitted successfully. Please wait for admin approval before logging in.";
            header("Location: SIGNUPST.php");
            exit();
        } else {
            $errors['general'] = "Error occurred during registration. Please try again.";
        }
        $stmt->close();
    }

    $conn->close();
    
    // Store form data and errors in session
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $errors;
    header("Location: SIGNUPST.php");
    exit();
}
?>

<?php if (!empty($errors)): ?>
    <div class="error-messages">
        <?php foreach ($errors as $field => $error): ?>
            <p class="error" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="success-message" style="color: green;">
        <?php 
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        ?>
    </div>
<?php endif;?> 
