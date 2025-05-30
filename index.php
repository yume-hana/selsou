<?php
session_start();
if (isset($_SESSION['registration_nbr'])) {
    header("Location: studenthome.php");
} else {
    header("Location: login.php");
}
exit;
?>