<?php
session_start();
include '../includes/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!empty($reservation_id) && in_array($action, ['Approved', 'Rejected'])) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ?, status_updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $action, $reservation_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            if ($action === 'Approved') {
                // Get reservation details
                $info_stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
                $info_stmt->bind_param("i", $reservation_id);
                $info_stmt->execute();
                $info = $info_stmt->get_result()->fetch_assoc();

                $selected_pc = $info['selected_pc'];
                $lab_number = $info['lab_number'];
                $id_no = $info['id_no'];
                $purpose = $info['purpose'];
                $date = $info['date'];
                $time_in = $info['time_in'];

                // Update PC status to used
                if (!empty($selected_pc)) {
                    $update_pc = $conn->prepare("UPDATE lab_pc_status SET status = 'Used' WHERE pc_number = ? AND lab_number = ?");
                    $update_pc->bind_param("ss", $selected_pc, $lab_number);
                    $update_pc->execute();
                }

                // Get user full name and session
                $user_stmt = $conn->prepare("SELECT firstname, middlename, lastname, remaining_sessions FROM users WHERE id_no = ?");
                $user_stmt->bind_param("s", $id_no);
                $user_stmt->execute();
                $user = $user_stmt->get_result()->fetch_assoc();

                $name = trim($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname']);
                $remaining = (int)$user['remaining_sessions'];

                // Check if already exists in sit_in_records
                $check = $conn->prepare("SELECT * FROM sit_in_records WHERE id_no = ? AND status IN ('Pending', 'Active')");
                $check->bind_param("s", $id_no);
                $check->execute();
                $check_result = $check->get_result();

                if ($check_result->num_rows === 0) {
                    $insert = $conn->prepare("INSERT INTO sit_in_records (id_no, name, purpose, lab_number, remaining_sessions, status, time_in, date) VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?)");
                    $insert->bind_param("ssssiss", $id_no, $name, $purpose, $lab_number, $remaining, $time_in, $date);
                    $insert->execute();
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

    header("Location: ../admin/admin-current.php");
    exit;
}
