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
    $new_total_points = $current_points + $points_to_add;

    $stmt_user = $conn->prepare("SELECT remaining_sessions FROM users WHERE id_no = ?");
    $stmt_user->bind_param("s", $id_no);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user = $user_result->fetch_assoc();

    $remaining_sessions = (int)$user['remaining_sessions'];
    $sessions_to_add = intdiv($new_total_points, 3);
    $should_add_session = $sessions_to_add > 0 && $remaining_sessions < 30;

    $conn->begin_transaction();
    try {
        // Always update points (no reset)
        $stmt1 = $conn->prepare("UPDATE sit_in_records SET points = ? WHERE id = ?");
        $stmt1->bind_param("ii", $new_total_points, $sit_in_id);
        $stmt1->execute();

        if ($should_add_session) {
            $actual_sessions_to_add = min($sessions_to_add, 30 - $remaining_sessions);
            $stmt2 = $conn->prepare("UPDATE users SET remaining_sessions = remaining_sessions + ? WHERE id_no = ?");
            $stmt2->bind_param("is", $actual_sessions_to_add, $id_no);
            $stmt2->execute();
            $_SESSION['notification_message'] = "Points updated and $actual_sessions_to_add session(s) added!";
        } else {
            $_SESSION['notification_message'] = "Points updated.";
        }

        $_SESSION['notification_type'] = "success";
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['notification_message'] = "Transaction failed: " . $e->getMessage();
        $_SESSION['notification_type'] = "error";
    }
} else {
    $_SESSION['notification_message'] = "Invalid request.";
    $_SESSION['notification_type'] = "error";
}

header("Location: ../admin/admin-records.php");
exit;
