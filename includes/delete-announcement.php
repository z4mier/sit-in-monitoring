<?php
include 'db-connection.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Unauthorized access");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement_id = intval($_POST['announcement_id']);

    $sql = "DELETE FROM announcement WHERE id=$announcement_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../admin/admin-announcements.php"); // Redirect after delete
        exit();
    } else {
        echo "❌ Error deleting: " . $conn->error;
    }
}

$conn->close();
?>
