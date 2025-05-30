<?php
require_once __DIR__ . '/includes/functions.php';

// Destroy all session data
$_SESSION = array();
session_destroy();

// Redirect to login with logout message
redirect('login.php?logout=1');
?>