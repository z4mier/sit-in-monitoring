<?php
session_start();
include '../includes/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!empty($reservation_id) && in_array($action, ['Approved', 'Rejected'])) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $reservation_id);
        if ($stmt->execute()) {
            $_SESSION['notification_message'] = "Reservation status updated to $action.";
        } else {
            $_SESSION['notification_message'] = "Failed to update reservation.";
        }
    } else {
        $_SESSION['notification_message'] = "Invalid action or reservation ID.";
    }
}

$_SESSION['success'] = "Reservation updated!";
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;


?>
