<?php
include 'db-connection.php';

$default_sessions = 30;

$sql = "UPDATE users SET remaining_sessions = ? WHERE role != 'admin' OR role IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $default_sessions);

if ($stmt->execute()) {
    // ✅ FIXED: Correct path to admin folder
    header("Location: ../admin/admin-students.php?reset_success=true");
} else {
    echo "Error resetting all sessions: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
