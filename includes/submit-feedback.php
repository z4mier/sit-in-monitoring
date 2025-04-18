<?php
session_start();
include 'db-connection.php';

if (!isset($_SESSION['user'])) {
    die("Unauthorized access.");
}

$user = $_SESSION['user'];
$username = $user['username'];

// Get id_no of the logged-in user
$stmt = $conn->prepare("SELECT id_no FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("ID Number not found.");
}

$row = $result->fetch_assoc();
$id_no = $row['id_no'];

// Process the feedback form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_id'], $_POST['feedback'])) {
    $record_id = intval($_POST['record_id']);
    $feedback = trim($_POST['feedback']);
    $date_today = date("Y-m-d");

    // ✅ Ensure the record_id exists in sit_in_records
    $check_stmt = $conn->prepare("SELECT id FROM sit_in_records WHERE id = ? AND id_no = ?");
    $check_stmt->bind_param("ii", $record_id, $id_no);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        die("Invalid sit-in record selected.");
    }

    // ✅ Insert into feedback
    $insert_stmt = $conn->prepare("INSERT INTO feedback (id_no, record_id, message, date) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("iiss", $id_no, $record_id, $feedback, $date_today);

    if ($insert_stmt->execute()) {
        header("Location: ../history.php?success=1");
        exit();
    } else {
        die("Failed to submit feedback: " . $insert_stmt->error);
    }
} else {
    die("Invalid submission.");
}
