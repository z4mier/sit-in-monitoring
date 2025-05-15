<?php
include 'db-connection.php'; 
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Unauthorized access");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement_text = $conn->real_escape_string($_POST['announcement_text']);
    $admin_name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Admin';

    $sql = "INSERT INTO announcement (announcement_text, created_by) 
            VALUES ('$announcement_text', '$admin_name')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../admin/admin-announcements.php"); 
        exit(); 
    } else {
        echo "Error!: " . $conn->error;
    }
}

$conn->close();
?>
