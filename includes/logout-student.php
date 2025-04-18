<?php
include 'db-connection.php';

if (isset($_GET['id'])) {
    $sit_in_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM sit_in_records WHERE id = ?");
    $stmt->bind_param("i", $sit_in_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Student has been logged out.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove sit-in.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No ID provided.']);
}
?>
