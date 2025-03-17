<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php"); // Go up one directory level and redirect
exit();
?>
