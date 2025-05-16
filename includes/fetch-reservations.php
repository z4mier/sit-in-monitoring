<?php
include 'db-connection.php';

// Format: "x min ago"
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return "$diff sec ago";
    if ($diff < 3600) return floor($diff / 60) . " min ago";
    if ($diff < 86400) return floor($diff / 3600) . " hrs ago";
    return date("M d, Y", $timestamp);
}

$query = "SELECT r.created_at, r.id_no, u.firstname, u.lastname, u.profile_picture
          FROM reservations r
          LEFT JOIN users u ON r.id_no = u.id_no
          WHERE r.status = 'Pending'
          ORDER BY r.created_at DESC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
        $filename = $row['profile_picture'];

        $imgPath = (!empty($filename) && file_exists("../uploads/" . $filename))
            ? "../uploads/" . $filename
            : '../uploads/default.png';


        $created_at = isset($row['created_at']) ? timeAgo($row['created_at']) : '';

        echo "
        <a href='admin-reservations.php' style='text-decoration: none; color: inherit;'>
          <div class='notif-item' style='display: flex; align-items: center; gap: 15px; padding: 12px; border: 1px solid #2a2f45; border-radius: 8px; background-color: #0d121e; margin-bottom: 12px; cursor: pointer;'>
            <img src='$imgPath' alt='Profile' style='width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid white;'>
            <div>
              <strong style='display: block;'>$fullName</strong>
              <span style='font-size: 14px; color: #ccc;'>submitted a reservation • $created_at</span>
            </div>
          </div>
        </a>
        ";
    }
} else {
    echo "<p style='text-align: center;'>No notifications found.</p>";
}
?>
