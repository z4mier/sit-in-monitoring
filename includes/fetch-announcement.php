<?php
include 'db-connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Delete Request
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    exit();
}

// Fetch Announcements
$sql = "SELECT id, announcement_text, created_by, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='announcement-item' id='announcement-".$row['id']."'>";
        echo "<button class='delete-btn' onclick='deleteAnnouncement(".$row['id'].")'><i class='fas fa-trash'></i></button>";
        echo "<p><strong>" . htmlspecialchars($row['created_by']) . "</strong> posted:</p>";
        echo "<p>" . nl2br(htmlspecialchars($row['announcement_text'])) . "</p>";
        echo "<small>📅 " . $row['created_at'] . "</small>";
        echo "</div>";
    }
} else {
    echo "<p>No announcements yet.</p>";
}

$conn->close();
?>

<script>
function deleteAnnouncement(id) {
    if (confirm("Are you sure you want to delete this announcement?")) {
        let formData = new FormData();
        formData.append("delete_id", id);

        fetch("fetch-announcement.php", {
            method: "POST",
            body: formData
        }).then(response => response.text()).then(data => {
            if (data.trim() === "success") {
                document.getElementById("announcement-" + id).remove();
            } else {
                alert("Error deleting announcement.");
            }
        }).catch(error => console.error("Error:", error));
    }
}
</script>

<style>

.delete-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    color: whitesmoke;
    font-size: 15px;
    cursor: pointer;
}
</style>
