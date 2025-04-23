<?php
include 'db-connection.php';

if (!isset($_GET['id'])) {
    echo "Missing student ID.";
    exit;
}

$student_id = $_GET['id'];
$default_sessions = 30;

$student_id = mysqli_real_escape_string($conn, $student_id);

$sql = "UPDATE users SET remaining_sessions = ? WHERE id_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $default_sessions, $student_id);

if ($stmt->execute()) {
    // ✅ FIXED: Redirect to /admin/admin-students.php
    header("Location: ../admin/admin-students.php?reset_success=true");
} else {
    echo "Failed to reset sessions: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
