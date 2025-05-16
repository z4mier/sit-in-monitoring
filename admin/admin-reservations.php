<?php
session_start();
include '../includes/db-connection.php';
$selected_lab = $_GET['lab'] ?? '';
$pcs = [];

// Initialize lab PC status
if (!empty($selected_lab)) {
  for ($i = 1; $i <= 30; $i++) {
    $pc = "PC $i";
    $check = $conn->prepare("SELECT id FROM lab_pc_status WHERE lab_number = ? AND pc_number = ?");
    $check->bind_param("ss", $selected_lab, $pc);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
      $insert = $conn->prepare("INSERT INTO lab_pc_status (lab_number, pc_number, status) VALUES (?, ?, 'Available')");
      $insert->bind_param("ss", $selected_lab, $pc);
      $insert->execute();
    }
  }

  $stmt = $conn->prepare("SELECT * FROM lab_pc_status WHERE lab_number = ?");
  $stmt->bind_param("s", $selected_lab);
  $stmt->execute();
  $pcs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch pending reservations JOINED with users to get name
$reservations = [];
$resQuery = "
  SELECT r.*, u.firstname, u.middlename, u.lastname
  FROM reservations r
  LEFT JOIN users u ON r.id_no = u.id_no
  WHERE r.status = 'Pending'
  ORDER BY r.created_at DESC
";
$resResult = $conn->query($resQuery);
if ($resResult && $resResult->num_rows > 0) {
  while ($row = $resResult->fetch_assoc()) {
    $reservations[] = $row;
  }
}

$notification_message = $_SESSION['notification_message'] ?? '';
$notification_type = $_SESSION['notification_type'] ?? '';
unset($_SESSION['notification_message'], $_SESSION['notification_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservation Controls</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; display: flex; }
    .main-content { margin-left: 80px; padding: 20px; flex: 1; }
    .sidebar:hover ~ .main-content { margin-left: 200px; width: calc(100% - 200px); }
    h1 { margin: 0 0 15px 0; font-size: 28px; }
    .dashboard-row { display: flex; gap: 20px; flex-wrap: wrap; }
    .form-box, .table-box { flex: 1; min-width: 0; padding: 20px; background-color: #0d121e; border: 2px solid white; border-radius: 20px; box-shadow: 0 0 10px rgba(255,255,255,0.2); max-height: 700px; overflow-y: auto; width: 100%; }
    .form-box h3, .table-box h3 { text-align: center; font-size: 20px; margin-bottom: 15px; }
    .filter-form { display: flex; gap: 10px; margin-bottom: 15px;}
    .filter-form select { flex: 1; padding: 10px 15px; border-radius: 20px; border: none; font-size: 14px;}
    .filter-form button { padding: 10px 15px; border-radius: 20px; border: none; font-size: 14px; background-color: #212b40; color: white; cursor: pointer;}
    .pc-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; padding-top: 10px; }
    .pc-tile { border-radius: 15px; text-align: center; padding: 12px; cursor: pointer; font-weight: bold; }
    .available { background-color: #0d121e; color: white; border: 2px solid #34d399; }
    .used { background-color: #0d121e; color: white; border: 2px solid #f87171; }
    .legend { display: flex; justify-content: space-between; margin-top: 20px; font-size: 14px;}
    .legend span { display: flex; align-items: center; gap: 8px; }
    .legend-box { width: 16px; height: 16px; border-radius: 4px; }
    .box-available { background-color: #34d399; }
    .box-used { background-color: #f87171; }
    .reservation-empty { text-align: center; color: #888; margin-top: 100px; }
    .submit-btn { margin-top: 15px; width: 100%; padding: 12px; background-color: #212b40; color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 15px; }
    .action-btn { padding: 5px 10px; border: none; border-radius: 10px; cursor: pointer; margin: 0 5px; }

    .notification {
      display: none;
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #181a25;
      color: white;
      padding: 15px 20px;
      border-radius: 20px;
      font-size: 14px;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.3s ease;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .notification.show { display: flex; opacity: 1; }
    .notification.success i { color: #4ade80; }
    .notification.error i { color: #f87171; }

    input[type="checkbox"] { display: none; }
    input[type="checkbox"]:checked + .pc-tile { opacity: 0.5; }
  </style>
</head>
<body>
<?php include '../includes/admin-sidebar.php'; ?>
<div class="main-content">
  <div style="padding-bottom: 20px; border-bottom: 2px solid #333; margin-bottom: 20px;">
    <h1>Admin - Reservation Controls</h1>
    <a href="admin-reservation-logs.php">
  <button style="margin-top: 10px; padding: 10px 18px; font-size: 15px; border-radius: 8px; background-color: white; color: 212b40; border: none; cursor: pointer;">
    View Reservation Logs
  </button>
</a>

  </div>
  <div class="dashboard-row">
    <div class="form-box">
      <h3><i class="fas fa-desktop"></i> Computer Controls</h3>
      <form class="filter-form" method="GET">
        <select name="lab" required>
          <option value="">Select Lab</option>
          <?php
          $labs = ['Mac Laboratory','517','524','526','528','530','540','544'];
          foreach ($labs as $lab) {
            echo "<option value='$lab'" . ($selected_lab === $lab ? ' selected' : '') . ">$lab</option>";
          }
          ?>
        </select>
        <button type="submit">Filter</button>
      </form>

      <?php if (!empty($pcs)): ?>
        <form method="POST" action="../includes/update-pc-status.php">
          <input type="hidden" name="lab" value="<?= htmlspecialchars($selected_lab) ?>">
          <div class="pc-grid">
            <?php foreach ($pcs as $pc): ?>
              <label>
                <input type="checkbox" name="pcs[]" value="<?= $pc['pc_number'] ?>" <?= $pc['status'] === 'Used' ? 'checked' : '' ?> />
                <div class="pc-tile <?= strtolower($pc['status']) ?>">
                  <i class="fas fa-desktop"></i><br><?= $pc['pc_number'] ?><br><small><?= $pc['status'] ?></small>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
          <button type="submit" class="submit-btn">Update Selected PCs</button>
        </form>
        <div class="legend">
          <span><div class="legend-box box-available"></div> Available</span>
          <span><div class="legend-box box-used"></div> Used</span>
        </div>
      <?php else: ?>
        <p class="reservation-empty">Please select a laboratory to view its available computers.</p>
      <?php endif; ?>
    </div>

    <div class="table-box">
      <h3><i class="fas fa-clock"></i> Reservation Request</h3>
      <?php if (!empty($reservations)): ?>
        <?php foreach ($reservations as $res): ?>
          <?php $full_name = trim($res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname']); ?>
          <div style="border: 1px solid #333; border-radius: 15px; padding: 20px; margin-bottom: 20px; background-color: #161d31;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); row-gap: 12px; column-gap: 15px;">
              <div><strong>ID No.:</strong> <?= htmlspecialchars($res['id_no']) ?></div>
              <div><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></div>
              <div><strong>Date:</strong> <?= htmlspecialchars($res['date']) ?></div>
              <div><strong>Time:</strong> <?= htmlspecialchars($res['time_in']) ?></div>
              <div><strong>Lab:</strong> <?= htmlspecialchars($res['lab_number']) ?></div>
              <div><strong>PC:</strong> <?= htmlspecialchars($res['selected_pc']) ?></div>
              <div><strong>Purpose:</strong> <?= htmlspecialchars($res['purpose']) ?></div>
              <div><strong>Status:</strong> <?= htmlspecialchars($res['status']) ?></div>
            </div>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
              <form method="POST" action="../includes/update-reservation.php">
                <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                <input type="hidden" name="action" value="Approved">
                <button type="submit" style="background-color:#10b981; color:white; padding:6px 12px; border:none; border-radius:10px;">Accept</button>
              </form>
              <form method="POST" action="../includes/update-reservation.php">
                <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                <input type="hidden" name="action" value="Rejected">
                <button type="submit" style="background-color:#ef4444; color:white; padding:6px 12px; border:none; border-radius:10px;">Reject</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="reservation-empty">No pending reservations found.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div id="notification" class="notification <?= $notification_type ?>">
  <i class="<?= $notification_type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle' ?>"></i>
  <span><?= htmlspecialchars($notification_message) ?></span>
</div>

<script>
  const notifBox = document.getElementById('notification');
  const shouldShow = <?= json_encode((bool)$notification_message) ?>;
  if (shouldShow) {
    notifBox.classList.add('show');
    setTimeout(() => {
      notifBox.classList.remove('show');
    }, 3000);
  }
</script>
</body>
</html>
