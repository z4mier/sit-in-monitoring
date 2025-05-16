<?php
session_start();
include '../includes/db-connection.php';

$sit_in_rows = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_no']) && isset($_POST['reward'])) {
        $id_no = $_POST['id_no'];

        $reward_stmt = $conn->prepare("UPDATE users SET points = points + 1 WHERE id_no = ?");
        $reward_stmt->bind_param("s", $id_no);

        if ($reward_stmt->execute()) {
            $_SESSION['notification_message'] = "1 point rewarded to ID $id_no!";
            $_SESSION['notification_type'] = "success";
        } else {
            $_SESSION['notification_message'] = "Failed to reward point.";
            $_SESSION['notification_type'] = "error";
        }

        header("Location: admin-current.php");
        exit();
    }
    if (isset($_POST['start_session'])) {
    $sit_in_id = $_POST['sit_in_id'];
    $sql = "UPDATE sit_in_records SET status = 'Active', time_in = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sit_in_id);

    if ($stmt->execute()) {
        $_SESSION['notification_message'] = "Session started successfully!";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification_message'] = "Failed to start session.";
        $_SESSION['notification_type'] = "error";
    }

    header("Location: admin-current.php");
    exit();
}


    if (isset($_POST['time_out'])) {
        $sit_in_id = $_POST['sit_in_id'];
        $time_out = date("Y-m-d H:i:s");

        $sql = "UPDATE sit_in_records SET status = 'Offline', time_out = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $time_out, $sit_in_id);

        if ($stmt->execute()) {
            $_SESSION['notification_message'] = "Student successfully ended session!";
            $_SESSION['notification_type'] = "success";
        } else {
            $_SESSION['notification_message'] = "Error while ending the session.";
            $_SESSION['notification_type'] = "error";
        }

        header("Location: admin-current.php");
        exit();
    }
}

$notification_message = $_SESSION['notification_message'] ?? '';
$notification_type = $_SESSION['notification_type'] ?? '';
unset($_SESSION['notification_message'], $_SESSION['notification_type']);

$sql = "SELECT r.id, r.id_no, r.name, r.purpose, r.lab_number, r.status, u.remaining_sessions
        FROM sit_in_records r
        JOIN users u ON r.id_no = u.id_no
        WHERE r.status IN ('Pending', 'Active')
        ORDER BY r.id DESC";


$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sit_in_id = htmlspecialchars($row['id']);
        $id_no = htmlspecialchars($row['id_no']);
        $name = htmlspecialchars($row['name']);
        $purpose = htmlspecialchars($row['purpose']);
        $lab_number = htmlspecialchars($row['lab_number']);
        $remaining_sessions = htmlspecialchars($row['remaining_sessions']);
        $status = htmlspecialchars($row['status']);

        $sit_in_rows .= "
        <tr>
            <td>$id_no</td>
            <td>$name</td>
            <td>$purpose</td>
            <td>$lab_number</td>
            <td>$remaining_sessions</td>
            <td>$status</td>
            <td>
  " . ($status === 'Pending' ? "
    <form method='POST' action='admin-current.php' style='display:inline-block;'>
        <input type='hidden' name='sit_in_id' value='$sit_in_id'>
        <button type='submit' name='start_session' class='logout-btn'>Start Session</button>
    </form>
  " : "
    <form method='POST' action='admin-current.php' style='display:inline-block; margin-right:5px;'>
        <input type='hidden' name='sit_in_id' value='$sit_in_id'>
        <button type='submit' name='time_out' class='logout-btn'>End Session</button>
    </form>
    <form method='POST' action='admin-current.php' style='display:inline-block;'>
        <input type='hidden' name='id_no' value='$id_no'>
        <input type='hidden' name='reward' value='1'>
        <button type='submit' title='Reward 1 point' style='background: none; border: none; cursor: pointer; color: gold; font-size: 18px;'>
            <i class='fas fa-star'></i>
        </button>
    </form>
  ") . "
</td>

        </tr>";
    }
} else {
    $sit_in_rows = "<tr><td colspan='7'>No active sit-in records found.</td></tr>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Current Sit-In</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; display: flex; background-color: #0d121e; color: #ffffff; }
    .main-content { margin-left: 80px; padding: 20px; flex: 1; }
    .sidebar:hover ~ .main-content { margin-left: 200px; }
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 2px solid #333; }
    .table-container { margin-top: 20px; border-radius: 10px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    table th, table td { padding: 15px; text-align: center; }
    table tr:nth-child(even) { background-color: #111524; }
    table tr:nth-child(odd) { background-color: #212b40; }
    thead tr { background-color: transparent !important; }
    table tr:hover { background-color: #181a25; }
    td:last-child { display: flex; justify-content: center; gap: 8px; }
    .logout-btn { background-color: white; color: #333; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold; cursor: pointer; }
    .logout-btn:hover { background-color: #f1f1f1; }
    .notification { display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #181a25; color: white; padding: 15px 25px; border-radius: 20px; font-size: 15px; z-index: 1000; display: flex; align-items: center; gap: 10px; opacity: 0; transition: opacity 0.5s ease-in-out; }
    .notification.show { opacity: 1; }
    .notification.success i { color: #4ade80; }
    .notification.error i { color: #f87171; }
  </style>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
  <header>
    <h1>Admin - Current Sit-In</h1>
  </header>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID Number</th>
          <th>Name</th>
          <th>Purpose</th>
          <th>Lab #</th>
          <th>Session</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="sitInTable">
        <?= $sit_in_rows ?>
      </tbody>
    </table>
  </div>
</div>

<div id="successNotification" class="notification <?= $notification_type ?>">
  <i class="<?= $notification_type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle' ?>"></i>
  <span id="notificationMessage"><?= $notification_message ?></span>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const message = <?= json_encode($notification_message) ?>;
    const type = <?= json_encode($notification_type) ?>;
    if (message) {
      showNotification(message, type);
    }
  });

  function showNotification(message, type) {
    const box = document.getElementById("successNotification");
    const icon = box.querySelector("i");
    const text = document.getElementById("notificationMessage");

    box.className = `notification ${type} show`;
    icon.className = type === "success" ? "fas fa-check-circle" : "fas fa-times-circle";
    text.textContent = message;

    setTimeout(() => {
      box.classList.remove("show");
    }, 3000);
  }
</script>

</body>
</html>
