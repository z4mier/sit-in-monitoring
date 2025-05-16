<?php
session_start();
include 'db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = trim($_POST['day'] ?? '');
    $lab = trim($_POST['lab'] ?? '');
    $time_in = $_POST['time_in'] ?? '';
    $time_out = $_POST['time_out'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $professor = trim($_POST['professor'] ?? '');

    // Basic validation
    if (empty($day) || empty($lab) || empty($time_in) || empty($time_out) || empty($subject) || empty($professor)) {
        $_SESSION['notification_message'] = "All fields are required.";
        $_SESSION['notification_type'] = "error";
        header("Location: ../admin/admin-laboratories.php");
        exit;
    }

    // Optional: check time logic
    if ($time_in >= $time_out) {
        $_SESSION['notification_message'] = "Time Out must be later than Time In.";
        $_SESSION['notification_type'] = "error";
        header("Location: ../admin/admin-laboratories.php");
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO lab_schedule (day, lab, time_in, time_out, subject, professor) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $day, $lab, $time_in, $time_out, $subject, $professor);

    if ($stmt->execute()) {
        $_SESSION['notification_message'] = "Schedule successfully added!";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification_message'] = "Error saving schedule.";
        $_SESSION['notification_type'] = "error";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../admin/admin-laboratories.php");
    exit;
}
?>
