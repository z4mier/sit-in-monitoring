<?php
session_start();
include 'db-connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_no = $_POST['id_no'];
    $name = $_POST['name'];
    $purpose = $_POST['purpose'];
    $lab_number = $_POST['lab_number'];
    $remaining_sessions = (int)$_POST['remaining_sessions'];

    if ($remaining_sessions <= 0) {
        $_SESSION['success_message'] = "Sit-In failed: No remaining sessions.";
        header("Location: ../admin/admin-current.php");
        exit();
    }

    $conn->begin_transaction();

    try {
        // 1. Insert into sit_in_records
        $insert_sql = "INSERT INTO sit_in_records (id_no, name, purpose, lab_number, remaining_sessions) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssi", $id_no, $name, $purpose, $lab_number, $remaining_sessions);
        $stmt->execute();

        // 2. Decrement remaining_sessions in users
        $new_remaining = $remaining_sessions - 1;
        $update_sql = "UPDATE users SET remaining_sessions = ? WHERE id_no = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("is", $new_remaining, $id_no);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = "Sit-In successfully recorded for $name!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['success_message'] = "Sit-In failed: " . $e->getMessage();
    }

    header("Location: ../admin/admin-current.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
