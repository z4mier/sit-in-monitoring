<?php
include 'db-connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_id = $_POST['old_id']; // Get old student ID
    $new_id = $_POST['id']; // New student ID (editable)
    $name = $_POST['name'];
    $yr_level = $_POST['yr_level'];
    $course = $_POST['course'];
    $remaining_sessions = $_POST['remaining_sessions'];

    // Split the full name into firstname & lastname
    $name_parts = explode(" ", $name);
    $firstname = $name_parts[0];
    $lastname = isset($name_parts[1]) ? $name_parts[1] : "";

    $sql = "UPDATE users SET id_no = ?, firstname = ?, lastname = ?, yr_level = ?, course = ?, remaining_sessions = ? WHERE id_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $new_id, $firstname, $lastname, $yr_level, $course, $remaining_sessions, $old_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>