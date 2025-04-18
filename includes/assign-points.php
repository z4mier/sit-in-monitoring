<?php
session_start();
include '../includes/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sit_in_id'], $_POST['points'])) {
    $sit_in_id = intval($_POST['sit_in_id']);
    $points_to_add = intval($_POST['points']);

    if ($points_to_add <= 0) {
        $_SESSION['notification_message'] = "Please enter a valid number of points.";
        $_SESSION['notification_type'] = "error";
        header("Location: ../admin/admin-records.php");
        exit;
    }

    // Fetch the sit-in record
    $stmt = $conn->prepare("SELECT id_no, points FROM sit_in_records WHERE id = ?");
    $stmt->bind_param("i", $sit_in_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();

    if (!$record) {
        $_SESSION['notification_message'] = "Sit-in record not found.";
        $_SESSION['notification_type'] = "error";
        header("Location: ../admin/admin-records.php");
        exit;
    }

    $id_no = $record['id_no'];
    $current_points = (int)$record['points'];
    $new_points = $current_points + $points_to_add;

    // Check if it hits or exceeds 3 points
    if ($new_points >= 3) {
        $extra_sessions = intdiv($new_points, 3);
        $remaining_points = $new_points % 3;

        // Reset points and update user session
        $conn->begin_transaction();
        try {
            // Update sit-in record
            $stmt1 = $conn->prepare("UPDATE sit_in_records SET points = ? WHERE id = ?");
            $stmt1->bind_param("ii", $remaining_points, $sit_in_id);
            $stmt1->execute();

            // Add sessions to user
            $stmt2 = $conn->prepare("UPDATE users SET remaining_sessions = remaining_sessions + ? WHERE id_no = ?");
            $stmt2->bind_param("is", $extra_sessions, $id_no);
            $stmt2->execute();

            $conn->commit();
            $_SESSION['notification_message'] = "Points assigned and session(s) added!";
            $_SESSION['notification_type'] = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['notification_message'] = "Transaction failed: " . $e->getMessage();
            $_SESSION['notification_type'] = "error";
        }
    } else {
        // Just update points if less than 3
        $stmt = $conn->prepare("UPDATE sit_in_records SET points = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_points, $sit_in_id);
        if ($stmt->execute()) {
            $_SESSION['notification_message'] = "Points updated.";
            $_SESSION['notification_type'] = "success";
        } else {
            $_SESSION['notification_message'] = "Failed to update points.";
            $_SESSION['notification_type'] = "error";
        }
    }
} else {
    $_SESSION['notification_message'] = "Invalid request.";
    $_SESSION['notification_type'] = "error";
}

header("Location: ../admin/admin-records.php");
exit;
