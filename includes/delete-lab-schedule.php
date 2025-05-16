<?php
session_start();
include '../includes/db-connection.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ✅ Corrected column name from schedule_id → id
    $sql = "DELETE FROM lab_schedule WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Lab schedule deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete lab schedule: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "No schedule ID provided.";
}

header("Location: ../admin/admin-laboratories.php");
exit();
?>
