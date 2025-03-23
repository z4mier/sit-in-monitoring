<?php
ini_set('display_errors', 1);
ob_start();
session_start();

$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idNumber = isset($_POST['idNumber']) ? trim($_POST['idNumber']) : '';
    $studentName = isset($_POST['studentName']) ? trim($_POST['studentName']) : '';
    $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : '';
    $lab = isset($_POST['lab']) ? trim($_POST['lab']) : '';
    $remainingSessions = isset($_POST['remainingSession']) ? (int) $_POST['remainingSession'] : 0;

    if (empty($idNumber) || empty($studentName) || empty($purpose) || empty($lab)) {
        die("Error: Missing required fields.");
    }

    $sql = "INSERT INTO sit_in_records (id_no, name, purpose, lab_number, remaining_sessions) 
            VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssi", $idNumber, $studentName, $purpose, $lab, $remainingSessions);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Student sit-in successfully!";
            $stmt->close();
            $conn->close();
            header("Location: ../admin/admin-current.php");
            exit();
        } else {
            die("Error inserting record: " . $stmt->error);
        }
    } else {
        die("Error in SQL preparation: " . $conn->error);
    }
}

$conn->close();
ob_end_flush();
?>
