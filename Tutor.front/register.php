<!-- the file of register.php -->
<?php
require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/db.php';
require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/functions.php';
session_start();

$errors = [];
$successMessage = '';
$formData = [
    'first_nameT' => '',
    'last_nameT' => '',
    'date_of_birthT' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'quality' => '',
    'gender' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name'])) {
    // ==========================
    // تسجيل حساب جديد - Sign Up
    // ==========================

    $formData = [
        'first_nameT' => sanitizeInput($_POST['first_name']),
        'last_nameT' => sanitizeInput($_POST['last_name']),
        'date_of_birthT' => sanitizeInput($_POST['dob']),
        'email' => sanitizeInput($_POST['email']),
        'phone' => sanitizeInput($_POST['phone']),
        'address' => sanitizeInput($_POST['address']),
        'quality' => sanitizeInput($_POST['quality'] ?? ''),
        'gender' => sanitizeInput($_POST['gender'] ?? '')
    ];
    $password = $_POST['password'] ?? '';

    // Validation التحقق من صحة البيانات
    if (empty($formData['first_nameT'])) $errors[] = "First name is required";
    if (empty($formData['last_nameT'])) $errors[] = "Last name is required";
    if (empty($formData['date_of_birthT'])) $errors[] = "Date of birth is required";
    else {
        $dob = new DateTime($formData['date_of_birthT']);
        $now = new DateTime();
        $age = $now->diff($dob)->y;
        if ($age < 13) $errors[] = "You must be at least 13 years old";
    }
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if (!preg_match('/^\+?[0-9]{10,15}$/', $formData['phone'])) $errors[] = "Invalid phone number format";
    if (empty($formData['address'])) $errors[] = "Address is required";

    $validQualities = ['teacher', 'student_master'];
    $validGenders = ['Male', 'Female'];
    $qualityDbValue = strtolower($formData['quality']);
    $genderDbValue = ucfirst(strtolower($formData['gender']));

    if (!in_array($qualityDbValue, $validQualities)) $errors[] = "Invalid qualification level";
    if (!in_array($genderDbValue, $validGenders)) $errors[] = "Invalid gender selection";

    // التحقق من الإيميل
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT Tutor_ID, statusT FROM tutor WHERE Email_addressT = ?");
            $stmt->execute([$formData['email']]);
            $user = $stmt->fetch();

            if ($user) {
                $status = $user['statusT'];
                if ($status === 'approved') {
                    session_regenerate_id(true); // لحماية الجلسة
                    $_SESSION['tutor_id'] = $user['Tutor_ID'];
                    header("Location: http://localhost/LMW-PROJET/Tutor.front/TutorHome.html");
                    exit();
                } elseif ($status === 'pending') {
                    $successMessage = "Your account is under review. Please wait for approval.";
                } elseif ($status === 'rejected') {
                    $errors[] = "Sorry, your application has been rejected.";
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                // جديد، نسجلوه
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO tutor 
                    (first_nameT, last_nameT, date_of_birthT, PasswordT, Email_addressT, 
                    phone_numberT, quality, gender, Address, statusT)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->execute([
                    $formData['first_nameT'],
                    $formData['last_nameT'],
                    $formData['date_of_birthT'],
                    $hashedPassword,
                    $formData['email'],
                    $formData['phone'],
                    $qualityDbValue,
                    $genderDbValue,
                    $formData['address']
                ]);
                $successMessage = "Your account is under review. Please wait for approval.";
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            $errors[] = "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>LeadMyWay</title>
    <link rel="icon" href="image/491004696_1414770356176410_912691794412146724_n.png" type="image/png" sizes="64x64">
    <link rel="stylesheet" href="http://localhost/LMW-PROJET/Tutor.front/animationSignIn.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="secondPage">
    <nav class="navbar">
        <img class="imgLogo" src="http://localhost/LMW-PROJET/Tutor.front/imagesTUTOR/491004696_1414770356176410_912691794412146724_n.png" alt="Lead My Way Logo">
        <div class="logo">Lead My Way</div>
    </nav>
    <div class="page2">
        <div class="signInSignUp">
            <div class="welcomeSign">
                <h1 class="welcomeSign1">WELCOME <h1 class="welcomeSign2">Back!</h1></h1>
                <h2 class="welcomeSign3">To keep connected with us please <h2 class="welcomeSign4">Sign in with your personal info</h2></h2>
                <a class="SignInAnimation" href="http://localhost/LMW-PROJET/Tutor.back/login.php"><p>Sign In</p></a>
            </div>
        </div>
        <div class="CreateTuteurAccount">
            <h2>Create Tutor’s Account</h2>
        </div>

        <!-- Form Sign Up -->
        <form class="infoTuteur" method="POST" action="">
            <p class="TextFirstNameBOX1">First name:</p>
            <input class="FirstNameBOX1" type="text" name="first_name" placeholder="First Name" required value="<?= htmlspecialchars($formData['first_nameT']) ?>">

            <p class="TextLastNameBOX1">Last name:</p>
            <input class="LastNameBOX1" type="text" name="last_name" placeholder="Last Name" required value="<?= htmlspecialchars($formData['last_nameT']) ?>">

            <p class="TextPasswordBOX1">Password:</p>
            <input class="PasswoerdBOX1" type="password" name="password" placeholder="Password" required>

            <p class="TextPhoneNumberBOX1">Phone Number:</p>
            <input class="PhoneNumberBOX" type="text" name="phone" placeholder="Phone Number" required value="<?= htmlspecialchars($formData['phone']) ?>">

            <p class="TextEmailAddressOX1">Email Address:</p>
            <input class="EmailAddressBOX" type="email" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($formData['email']) ?>">

            <p class="TextAddressBOX1">Address:</p>
            <input class="AddressBOX" type="text" name="address" placeholder="Address" required value="<?= htmlspecialchars($formData['address']) ?>">

            <p class="question1">What is your date of birth?</p>
            <input class="DateOfBirth" type="date" max="2008-12-31" name="dob" value="<?= htmlspecialchars($formData['date_of_birthT']) ?>" required>

            <p class="GenderTuteurQuestion">What is your Gender?</p>
            <select class="MaleFemaleBOX" name="gender" required>
                <option value="Male" <?= $formData['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $formData['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>

            <p class="QualityTuteurQuestion">What is your Quality?</p>
            <select class="QualityTuteurBOX" name="quality" required>
                <option value="teacher" <?= $formData['quality'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                <option value="student_master" <?= $formData['quality'] === 'student_master' ? 'selected' : '' ?>>Student Master</option>
            </select>

            <!-- عرض رسائل الأخطاء والنجاح فوق زر Sign Up -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="margin-top: -9px; margin-left: 92px; color: red; font-weight: bold; font-family: nunito, sans-serif;">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!empty($successMessage)): ?>
                <div class="alert alert-success" style="margin-top: 3px; margin-left: 60px; color: green; font-size: 14px; font-weight: bold; font-family: nunito, sans-serif;">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <button type="submit" id="isSignUnPage" class="SignUPToHome">
                <p>Sign Up</p>
            </button>
        </form>
    </div>
</div>
<script src="animationSignIn.js"></script>
</body>
</html>
