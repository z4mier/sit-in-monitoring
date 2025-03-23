<?php
include '../includes/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_GET['id'];

    // Prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM users WHERE id_no = ?");
    $stmt->bind_param("s", $studentId);

    if ($stmt->execute()) {
        http_response_code(200); // Success response
    } else {
        http_response_code(500); // Error response
    }

    $stmt->close();
    $conn->close();
}
?>
