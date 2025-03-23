<?php
session_start();
include 'db-connection.php';

if (isset($_GET['id'])) {
    $sit_in_id = $_GET['id'];

    $sql = "DELETE FROM sit_in_records WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sit_in_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User successfully logged out."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Error: No ID provided."]);
}
?>
