<?php
require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// حماية الوصول
if (!isset($_SESSION['tutor_id']) || $_SESSION['user_type'] !== 'tutor') {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: http://localhost/LMW-PROJET/Tutor.back/login.php");
    exit;
}

$tutor_id = $_SESSION['tutor_id'];

// الاتصال بقاعدة البيانات
function getDB() {
    $host = 'localhost';
    $db = 'your_database_name';
    $user = 'your_db_user';
    $pass = 'your_db_password';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    return new PDO($dsn, $user, $pass, $options);
}

$db = getDB();

// تحديث البيانات عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_nameT'] ?? '';
    $last_name = $_POST['last_nameT'] ?? '';
    $phone = $_POST['phone_numberT'] ?? '';
    $email = $_POST['Email_addressT'] ?? '';
    $address = $_POST['Address'] ?? '';

    $stmt = $db->prepare("UPDATE tutors SET first_nameT = ?, last_nameT = ?, phone_numberT = ?, Email_addressT = ?, Address = ? WHERE Tutor_ID = ?");
    $stmt->execute([$first_name, $last_name, $phone, $email, $address, $tutor_id]);

    $message = "Profile updated successfully.";
}

// جلب بيانات المعلم
$stmt = $db->prepare("SELECT first_nameT, last_nameT, phone_numberT, Email_addressT, Address FROM tutors WHERE Tutor_ID = ?");
$stmt->execute([$tutor_id]);
$tutor = $stmt->fetch();

if (!$tutor) {
    die("Tutor not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tutor Profile</title>
</head>
<body>

<h2>Tutor Profile</h2>

<?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="POST">
  <label>First Name</label><br>
  <input type="text" name="first_nameT" value="<?= htmlspecialchars($tutor['first_nameT']) ?>"><br><br>

  <label>Last Name</label><br>
  <input type="text" name="last_nameT" value="<?= htmlspecialchars($tutor['last_nameT']) ?>"><br><br>

  <label>Phone Number</label><br>
  <input type="tel" name="phone_numberT" value="<?= htmlspecialchars($tutor['phone_numberT']) ?>"><br><br>

  <label>Email</label><br>
  <input type="email" name="Email_addressT" value="<?= htmlspecialchars($tutor['Email_addressT']) ?>"><br><br>

  <label>Address</label><br>
  <textarea name="Address"><?= htmlspecialchars($tutor['Address']) ?></textarea><br><br>

  <button type="submit">Save Profile</button>
</form>

</body>
</html>
