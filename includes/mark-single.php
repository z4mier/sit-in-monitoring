<?php
include 'db-connection.php';

if (isset($_POST['id'])) {
  $id = (int)$_POST['id'];
  $conn->query("UPDATE reservations SET status = 'Seen' WHERE id = $id");
}
?>
