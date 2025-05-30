<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(" Connection failed: " . $conn->connect_error);
}

// Uncomment this line below to confirm successful connection
// echo " Connected to the database successfully!";
?>
