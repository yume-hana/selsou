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
    // Sanitize inputs
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

    // Validation
    if (empty($formData['first_nameT'])) $errors[] = "First name is required";
    if (empty($formData['last_nameT'])) $errors[] = "Last name is required";
    if (empty($formData['date_of_birthT'])) $errors[] = "Date of birth is required";
    
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if (!preg_match('/^\+?[0-9]{10,15}$/', $formData['phone'])) {
        $errors[] = "Invalid phone number format";
    }
    
    if (empty($formData['address'])) $errors[] = "Address is required";
    if (!in_array($formData['quality'], ['Master', 'Phd', '...'])) {
        $errors[] = "Invalid qualification level";
    }
    if (!in_array($formData['gender'], ['male', 'female'])) {
        $errors[] = "Invalid gender selection";
    }

    // Check email uniqueness
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT Tutor_ID FROM tutor WHERE Email_addressT = ?");
            $stmt->execute([$formData['email']]);
            if ($stmt->fetch()) {
                $errors[] = "Email already registered";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Register user
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
    <title>Tutor Registration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="registration-container">
        <h2>Tutor Registration</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_nameT" required 
                       value="<?= htmlspecialchars($formData['first_nameT']) ?>">
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_nameT" required 
                       value="<?= htmlspecialchars($formData['last_nameT']) ?>">
            </div>

            <div class="form-group">
                <label>Date of Birth:</label>
                <input type="date" name="date_of_birthT" required 
                       value="<?= htmlspecialchars($formData['date_of_birthT']) ?>">
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required 
                       value="<?= htmlspecialchars($formData['email']) ?>">
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required minlength="8">
            </div>

            <div class="form-group">
                <label>Phone Number:</label>
                <input type="tel" name="phone" required 
                       value="<?= htmlspecialchars($formData['phone']) ?>"
                       pattern="\+?[0-9]{10,15}">
            </div>

            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" required><?= htmlspecialchars($formData['address']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Qualification Level:</label>
                <select name="quality" required>
                    <option value="">Select qualification</option>
                    <option value="Beginner" <?= $formData['quality'] === 'Master' ? 'selected' : '' ?>>Master</option>
                    <option value="Intermediate" <?= $formData['quality'] === 'Phd' ? 'selected' : '' ?>>Phd</option>
                    <option value="Advanced" <?= $formData['quality'] === '....' ? 'selected' : '' ?>>...</option>
                </select>
            </div>

            <div class="form-group">
                <label>Gender:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="male" 
                               <?= $formData['gender'] === 'male' ? 'checked' : '' ?>> Male
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female" 
                               <?= $formData['gender'] === 'female' ? 'checked' : '' ?>> Female
                    </label>
                    
                </div>
            </div>

            <button type="submit" class="btn btn-register">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>