<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "sysarch");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $idNumber = $_POST['idNumber'];
    $studentName = $_POST['studentName']; // Name is directly captured from the form
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];
    $remainingSessions = $_POST['remainingSession'];

    // Insert data into `sit_in_records` table
    $sql = "INSERT INTO sit_in_records (id_no, name, purpose, lab_number, remaining_sessions) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $idNumber, $studentName, $purpose, $lab, $remainingSessions);

    if ($stmt->execute()) {
        // Redirect back to Admin-Students page after successful submission
        header("Location: ../admin/admin-students.php");
        exit();
    } else {
        echo "Error inserting record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
