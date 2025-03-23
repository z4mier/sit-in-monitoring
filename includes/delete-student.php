<?php
include 'db-connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = (int)$_GET['id']; 

    $sql = "DELETE FROM users WHERE id_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        // If deletion is successful, redirect to admin-students.php with a success message
        echo "<script>
                alert('Student deleted successfully!');
                window.location.href = '../admin/admin-students.php';
              </script>";
    } else {
        // Handle SQL execution errors
        echo "<script>
                alert('Error deleting student: " . $stmt->error . "');
                window.location.href = '../admin/admin-students.php';
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert('Invalid or missing ID parameter.');
            window.location.href = '../admin/admin-students.php';
          </script>";
}

$conn->close();
