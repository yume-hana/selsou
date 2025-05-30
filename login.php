<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session state
error_log("Session status: " . session_status());
error_log("Current session ID: " . session_id());
error_log("Current working directory: " . getcwd());

ob_start();
include("db.php");
include ("auth.php");

$errors = [];

// جلب البيانات من الكوكيز
$email_from_cookie = $_COOKIE['ST_email_address'] ?? '';
$password_from_cookie = $_COOKIE['ST_password'] ?? '';

// تهيئة القيم الافتراضية للعرض في الفورم
$email_value = '';
$password_value = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["ST_email_address"]);
    $password = $_POST["ST_password"];
    $remember = isset($_POST["remember"]);
    
    // حفظ القيم للعرض بعد محاولة الدخول
    $email_value = $email;
    $password_value = $password;

    // التحقق من صحة البيانات
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['ST_email_address'] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors['ST_password'] = "Password is required.";
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM student WHERE ST_email_address = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();

            if (password_verify($password, $student["ST_password"])) {
                if ($student['status'] === 'approved') {
                    // Set all required session variables
                    $_SESSION['registration_nbr'] = $student['registration_nbr'];
                    $_SESSION['student_name'] = $student['ST_first_name'];
                    $_SESSION['status'] = $student['status'];
                    $_SESSION['user_type'] = 'student';

                    // Handle remember me cookie
                    if ($remember) {
                        setcookie("ST_email_address", $email, time() + (86400 * 30), "/");
                        setcookie("ST_password", $password, time() + (86400 * 30), "/");
                    } else {
                        setcookie("ST_email_address", "", time() - 3600, "/");
                        setcookie("ST_password", "", time() - 3600, "/");
                    }

                    // Debug session
                    error_log("Session variables set: " . print_r($_SESSION, true));
                    
                    // Clear any output buffers
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    
                    // Ensure headers are not sent before redirect
                    if (!headers_sent()) {
                        error_log("Redirecting to studentFAQ.php");
                        header("Location: studentFAQ.php");
                        exit();
                    } else {
                        error_log("Headers already sent. Cannot redirect.");
                        echo "Login successful. <a href='studentFAQ.php'>Click here to continue</a>";
                    }
                } elseif ($student['status'] === 'pending') {
                    $errors['status'] = "Your registration is still pending admin approval.";
                } else {
                    $errors['status'] = "Your registration has been rejected.";
                }
            } else {
                $errors['login'] = "Incorrect email or password.";
            }
        } else {
            $errors['login'] = "Incorrect email or password.";
        }
        $stmt->close();
    }
    $conn->close();
} else {
    $email_from_cookie = $_COOKIE['ST_email_address'] ?? "";
    $password_from_cookie = $_COOKIE['ST_password'] ?? "";
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="animationSignIn.css">
</head>
<body>
    <div class="page22">
        <!-- Left: Login form and sign up link -->
        <div class="infoTuteurLOGIN">
            <h2>Student Login</h2>
            <?php
            if (isset($errors['status'])) echo "<p style='color:red;'>{$errors['status']}</p>";
            if (isset($errors['login'])) echo "<p style='color:red;'>{$errors['login']}</p>";
            ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="loginForm">
                <p class="TextEmailAddressOX11">Email Address:</p>
                <input type="email" class="EmailAddressBOX2" id="email" name="ST_email_address" required 
                    placeholder="Enter your email" value="<?php echo htmlspecialchars($email_from_cookie); ?>">
                <?php if (isset($errors['ST_email_address'])) echo '<span class="error">' . $errors['ST_email_address'] . '</span>'; ?>

                <p class="TextPasswordBOX11">Password:</p>
                <input type="password" class="PasswoerdBOX11" id="password" name="ST_password" required 
                    placeholder="Enter your password" value="<?php echo htmlspecialchars($password_from_cookie); ?>">
                <?php if (isset($errors['ST_password'])) echo '<span class="error">' . $errors['ST_password'] . '</span>'; ?>

                <div style="position:absolute; top:220px; left:70px;">
                    <label>
                        <input type="checkbox" name="remember" <?php echo $email_from_cookie ? 'checked' : ''; ?>>
                        Remember me
                    </label>
                </div>

                <button type="submit" class="SignUPToHome" id="signInButton">
                    <p>Sign In</p>
                </button>
                <div class="signup-link">
                    Don't have an account? <a href="SIGNUPST.php">Sign Up</a>
                </div>
            </form>
        </div>
        <!-- Right: Welcome and image -->
        <div class="signInSignUp2">
            <div class="welcomeSign2">
                <h1 class="welcomeSign11">WELCOME!</h1>
                <h2 class="welcomeSign33">Enter your personal details<br>and start your journey with us</h2>
                <h2 class="welcomeSign44">Sign In To Lead My Way</h2>
            </div>
        </div>
    </div>
</body>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    console.log('Form submitted');
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
        return;
    }
    
    // Log form data
    console.log('Email:', email);
    console.log('Password length:', password.length);
    
    // Let the form submit normally
    return true;
});
</script>
</html>
