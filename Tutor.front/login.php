<!-- the file of login.php -->
<?php

require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/db.php';
require_once 'C:/xampp/htdocs/LMW-PROJET/Tutor.back/includes/functions.php';



$error = '';
$email = '';

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT Tutor_ID, PasswordT, first_nameT FROM tutor WHERE Email_addressT = ?");
            $stmt->execute([$email]);
            $tutor = $stmt->fetch();

            if ($tutor && password_verify($password, $tutor['PasswordT'])) {
                session_regenerate_id(true);

$_SESSION['tutor_id'] = $tutor['Tutor_ID'];
error_log("SESSION SET - Tutor ID: " . $_SESSION['tutor_id']);
$_SESSION['tutor_email'] = $email;
$_SESSION['tutor_name'] = $tutor['first_nameT'];
$_SESSION['last_login'] = time();
$_SESSION['user_type'] = 'tutor';


                header("Location: http://localhost/LMW-PROJET/Tutor.front/TutorHome.html");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } catch (PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}

$registrationSuccess = isset($_SESSION['registration_success']);
if ($registrationSuccess) {
    unset($_SESSION['registration_success']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LeadMyWay - Sign In</title>
    <link rel="icon" href="http://localhost/LMW-PROJET/Tutor.front/imagesTUTOR/491004696_1414770356176410_912691794412146724_n.png" type="image/png" sizes="64x64" />
    <link rel="stylesheet" href="http://localhost/LMW-PROJET/Tutor.front/animationSignIn.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="secondPage">
        <nav class="navbar">
            <img class="imgLogo" src="http://localhost/LMW-PROJET/Tutor.front/imagesTUTOR/491004696_1414770356176410_912691794412146724_n.png" alt="Lead My Way Logo" />
            <div class="logo">Lead My Way</div>
        </nav>
        <div class="page22">
            <div class="signInSignUp2">
                <div class="welcomeSign2">
                    <h1 class="welcomeSign11">WELCOME !</h1>
                    <h2 class="welcomeSign33">
                        Enter your personal details
                        <h2 class="welcomeSign44">and start journey with us</h2>
                    </h2>
                    <a class="SignInAnimation2" href="http://localhost/LMW-PROJET/Tutor.back/register.php"><p>Sign Up</p></a>
                </div>
            </div>
            <div class="LOGINTuteurAccount">
                <h2>Sign In To Lead My Way</h2>
            </div>

            <?php if ($registrationSuccess): ?>
                <div class="alert alert-success">
                    Registration successful! Please log in.
                </div>
            <?php endif; ?>

            <form class="infoTuteurLOGIN" method="POST" action="login.php">
                <p class="TextEmailAddressOX11">Email Address:</p>
                <input class="EmailAddressBOX2" type="email" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($email) ?>" />

                <p class="TextPasswordBOX11">Password:</p>
                <input class="PasswoerdBOX11" type="password" name="password" placeholder="Password" required />

                <button id="isSignInPage" class="SignUPToHome" type="submit">
                    <p>Sign In</p>
                </button>

                <?php if ($error): ?>
                    <div class="alert alert-danger" style="margin-top: 10px; margin-left: 100px; color: red; font-weight: bold; font-family: nunito, sans-serif;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script src="animationSignIn.js"></script>
</body>

</html>
