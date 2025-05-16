<?php
include 'db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);

    // Get image file to delete
    $get = $conn->prepare("SELECT image_path FROM lab_resources WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $result = $get->get_result();
    $file = $result->fetch_assoc()['image_path'] ?? '';

    // Delete DB record
    $stmt = $conn->prepare("DELETE FROM lab_resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($file && file_exists("../uploads/$file")) {
            unlink("../uploads/$file");
        }
        echo 'success';
    } else {
        echo 'fail';
    }
}
?>
