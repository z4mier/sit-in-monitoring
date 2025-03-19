<?php
$servername = "localhost";  // Change if using a remote server
$username = "root";         // Default XAMPP MySQL user
$password = "";             // Default XAMPP has no password
$database = "sysarch";      // Make sure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
