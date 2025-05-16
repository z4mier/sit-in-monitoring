
<?php
session_start();
include 'db-connection.php';

// Debug aid — remove later
// error_reporting(E_ALL); ini_set('display_errors', 1);

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id_no'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$id_no = $_SESSION['user']['id_no'];

$sql = "SELECT status, status_updated_at AS timestamp
        FROM reservations
        WHERE id_no = ? AND status IN ('Approved', 'Rejected') 
        AND status_updated_at IS NOT NULL 
        ORDER BY status_updated_at DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_no);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

header('Content-Type: application/json');
echo json_encode($notifications);
?>
