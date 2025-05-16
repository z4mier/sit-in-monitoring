<?php
session_start();
include 'db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $day = trim($_POST['day']);
    $lab = trim($_POST['lab']);
    $time_in = $_POST['time_in'];
    $time_out = $_POST['time_out'];
    $subject = trim($_POST['subject']);
    $professor = trim($_POST['professor']);

    if (empty($day) || empty($lab) || empty($time_in) || empty($time_out) || empty($subject) || empty($professor)) {
        $_SESSION['notification_message'] = "All fields are required.";
        $_SESSION['notification_type'] = "error";
        header("Location: ../admin/admin-laboratories.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE lab_schedule SET day=?, lab=?, time_in=?, time_out=?, subject=?, professor=? WHERE id=?");
    $stmt->bind_param("ssssssi", $day, $lab, $time_in, $time_out, $subject, $professor, $id);

    if ($stmt->execute()) {
        $_SESSION['notification_message'] = "Schedule updated successfully.";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification_message'] = "Error updating schedule.";
        $_SESSION['notification_type'] = "error";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../admin/admin-laboratories.php");
    exit;
}
