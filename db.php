<?php
$host = "localhost";  // phpMyAdmin is hosted locally
$user = "root";       // Default username for phpMyAdmin
$password = "root123";       // Default password is empty in XAMPP/WAMP
$database = "art_gallery"; // Change to your database name

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
} 
?>
