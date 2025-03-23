<?php
// Include database connection
include '../includes/db-connection.php';

// Check if data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_no = htmlspecialchars($_POST['id_no']);
    $name = htmlspecialchars($_POST['name']);
    $yr_level = htmlspecialchars($_POST['yr_level']);
    $course = htmlspecialchars($_POST['course']);
    $remaining_sessions = htmlspecialchars($_POST['remaining_sessions']);

    // Update query
    $sql = "UPDATE users 
            SET firstname = ?, lastname = ?, yr_level = ?, course = ?, remaining_sessions = ?
            WHERE id_no = ?";

    // Split name into first and last names
    $name_parts = explode(" ", $name, 2);
    $firstname = $name_parts[0];
    $lastname = isset($name_parts[1]) ? $name_parts[1] : "";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $firstname, $lastname, $yr_level, $course, $remaining_sessions, $id_no);

    if ($stmt->execute()) {
        echo "Student updated successfully.";
        header("Location: ../admin-students.php"); // Redirect back to admin page
        exit;
    } else {
        echo "Error updating student.";
    }

    $stmt->close();
    $conn->close();
}
?>
