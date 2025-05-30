<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'first_nameT' => sanitizeInput($_POST['first_nameT']),
        'last_nameT' => sanitizeInput($_POST['last_nameT']),
        'date_of_birthT' => sanitizeInput($_POST['date_of_birthT']),
        'email' => sanitizeInput($_POST['email']),
        'phone' => sanitizeInput($_POST['phone']),
        'address' => sanitizeInput($_POST['address']),
        'quality' => sanitizeInput($_POST['quality'] ?? ''),
        'gender' => sanitizeInput($_POST['gender'] ?? '')
    ];
    $password = $_POST['password'] ?? '';

    // Validation (same as original register.php)
    if (empty($formData['first_nameT'])) $errors[] = "First name is required";
    if (empty($formData['last_nameT'])) $errors[] = "Last name is required";
    if (empty($formData['date_of_birthT'])) $errors[] = "Date of birth is required";
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if (!preg_match('/^\+?[0-9]{10,15}$/', $formData['phone'])) $errors[] = "Invalid phone number format";
    if (empty($formData['address'])) $errors[] = "Address is required";
    if (!in_array($formData['quality'], ['Master', 'Phd'])) $errors[] = "Invalid qualification level";
    if (!in_array($formData['gender'], ['male', 'female'])) $errors[] = "Invalid gender selection";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT Tutor_ID FROM tutor WHERE Email_addressT = ?");
            $stmt->execute([$formData['email']]);
            if ($stmt->fetch()) $errors[] = "Email already registered";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO tutor 
                (first_nameT, last_nameT, date_of_birthT, PasswordT, Email_addressT, 
                 phone_numberT, quality, gender, Address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $formData['first_nameT'],
                $formData['last_nameT'],
                $formData['date_of_birthT'],
                $hashedPassword,
                $formData['email'],
                $formData['phone'],
                $formData['quality'],
                $formData['gender'],
                $formData['address']
            ]);
            
            $_SESSION['registration_success'] = true;
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animated Tutor Registration</title>
    <link rel="stylesheet" href="assets/animated_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="secondPage">
        <div class="page2">
            <div class="signInSignUp">
                <div class="welcomeSign">
                    <h1>WELCOME !</h1>
                    <h2>Enter your details</h2>
                </div>
            </div>
            <div class="CreateTuteurAccount">
                <h2>Create Tutor's Account</h2>
            </div>
            <div class="infoTuteur">
                <?php if (!empty($errors)): ?>
                    <div class="error-container">
                        <?php foreach ($errors as $error): ?>
                            <p class="error"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <p class="TextFirstNameBOX1">first name:</p>
                    <input class="FirstNameBOX1" type="text" name="first_nameT" 
                           placeholder="First Name" required
                           value="<?= htmlspecialchars($formData['first_nameT']) ?>">

                    <p class="TextLastNameBOX1">last name:</p>
                    <input class="LastNameBOX1" type="text" name="last_nameT"
                           placeholder="Last Name" required
                           value="<?= htmlspecialchars($formData['last_nameT']) ?>">

                    <p class="TextPasswordBOX1">Password:</p>
                    <input class="PasswoerdBOX1" type="password" name="password"
                           placeholder="Password" required>

                    <p class="TextPhoneNumberBOX1">Phone Number:</p>
                    <input class="PhoneNumberBOX" type="text" name="phone"
                           placeholder="Phone Number" required
                           value="<?= htmlspecialchars($formData['phone']) ?>">

                    <p class="TextEmailAddressOX1">Email Address:</p>
                    <input class="EmailAddressBOX" type="email" name="email"
                           placeholder="Email Address" required
                           value="<?= htmlspecialchars($formData['email']) ?>">

                    <p class="TextAddressBOX1">Address:</p>
                    <input class="AddressBOX" type="text" name="address"
                           placeholder="Address" required
                           value="<?= htmlspecialchars($formData['address']) ?>">

                    <p class="question1">What is your date of birth?</p>
                    <input class="DateOfBirth" type="date" name="date_of_birthT"
                           max="2008-12-31" required
                           value="<?= htmlspecialchars($formData['date_of_birthT']) ?>">

                    <p class="GenderTuteurQuestion">What is your Gender?</p>
                    <select class="MaleFemaleBOX" name="gender">
                        <option value="male" <?= $formData['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $formData['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    </select>

                    <p class="QualityTuteurQuestion">What is your Quality?</p>
                    <select class="QualityTuteurBOX" name="quality">
                        <option value="Master" <?= $formData['quality'] === 'Master' ? 'selected' : '' ?>>Master</option>
                        <option value="Phd" <?= $formData['quality'] === 'Phd' ? 'selected' : '' ?>>PhD</option>
                    </select>

                    <button class="SignUPToHome" type="submit">
                        <p>Sign Up</p>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
