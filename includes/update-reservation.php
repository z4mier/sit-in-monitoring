<?php
session_start();
include '../includes/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!empty($reservation_id) && in_array($action, ['Approved', 'Rejected'])) {
        // Update reservation status
        $stmt = $conn->prepare("UPDATE reservations SET status = ?, status_updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $action, $reservation_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // If approved, mark the selected PC as Used
            if ($action === 'Approved') {
                $info_stmt = $conn->prepare("SELECT selected_pc, lab_number FROM reservations WHERE id = ?");
                $info_stmt->bind_param("i", $reservation_id);
                $info_stmt->execute();
                $info_result = $info_stmt->get_result();

                if ($info_result->num_rows > 0) {
                    $info = $info_result->fetch_assoc();
                    $selected_pc = $info['selected_pc'];
                    $lab_number = $info['lab_number'];

                    if (!empty($selected_pc)) {
                        $update_pc = $conn->prepare("UPDATE lab_pc_status SET status = 'Used' WHERE pc_number = ? AND lab_number = ?");
                        $update_pc->bind_param("ss", $selected_pc, $lab_number);
                        $update_pc->execute();
                    }
                }
            }

            $_SESSION['notification_message'] = "Reservation has been {$action}.";
            $_SESSION['notification_type'] = $action === 'Approved' ? 'success' : 'error';
        } else {
            $_SESSION['notification_message'] = "Failed to update reservation.";
            $_SESSION['notification_type'] = "error";
        }
    } else {
        $_SESSION['notification_message'] = "Invalid action or reservation ID.";
        $_SESSION['notification_type'] = "error";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
