<?php
include 'db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = $_POST['title'] ?? '';
    $link = $_POST['link'] ?? '';

    $image_name = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $image = $_FILES['image'];
        $upload_dir = '../uploads/';
        $image_name = uniqid() . '-' . basename($image['name']);
        $target = $upload_dir . $image_name;
        if (!move_uploaded_file($image['tmp_name'], $target)) {
            echo 'fail';
            exit;
        }

        // Optional: delete old image
        $res = $conn->prepare("SELECT image_path FROM lab_resources WHERE id = ?");
        $res->bind_param("i", $id);
        $res->execute();
        $old = $res->get_result()->fetch_assoc();
        if ($old && file_exists($upload_dir . $old['image_path'])) {
            unlink($upload_dir . $old['image_path']);
        }
    }

    if ($image_name) {
        $stmt = $conn->prepare("UPDATE lab_resources SET title = ?, link = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $link, $image_name, $id);
    } else {
        $stmt = $conn->prepare("UPDATE lab_resources SET title = ?, link = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $link, $id);
    }

    echo $stmt->execute() ? 'success' : 'fail';
}
?>
