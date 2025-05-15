<?php
include 'db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lab'])) {
    $lab = $_POST['lab'];
    $selected_pcs = isset($_POST['pcs']) ? $_POST['pcs'] : [];

    // Get all current PCs for the lab
    $stmt = $conn->prepare("SELECT pc_number FROM lab_pc_status WHERE lab_number = ?");
    $stmt->bind_param("s", $lab);
    $stmt->execute();
    $result = $stmt->get_result();

    // Update each PC status based on whether it's in the submitted list
    while ($row = $result->fetch_assoc()) {
        $pc = $row['pc_number'];
        $new_status = in_array($pc, $selected_pcs) ? 'Used' : 'Available';

        $update = $conn->prepare("UPDATE lab_pc_status SET status = ? WHERE lab_number = ? AND pc_number = ?");
        $update->bind_param("sss", $new_status, $lab, $pc);
        $update->execute();
    }

    $conn->close();
    header("Location: ../admin/admin-reservations.php?lab=" . urlencode($lab));
    exit();
} else {
    echo "Invalid request.";
}
?>
