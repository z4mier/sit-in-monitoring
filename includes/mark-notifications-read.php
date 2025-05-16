<?php
include 'db-connection.php'; // or connect.php, adjust based on your setup

// Mark all pending reservations as read (e.g., change status or flag)
$conn->query("UPDATE reservations SET status = 'Seen' WHERE status = 'Pending'");
echo "done";
?>
