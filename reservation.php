<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "sysarch");

$user = $_SESSION['user'];
$username = $user['username'];

$query = $conn->prepare("SELECT * FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$user_result = $query->get_result();
if ($user_result->num_rows === 0) {
    echo "<script>alert('Missing valid student data.'); window.location.href = 'index.php';</script>";
    exit();
}
$student = $user_result->fetch_assoc();
$id_no = $student['id_no'];
$full_name = $student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname'];
$remaining_session = $student['remaining_sessions'];

$lab_options = ['Mac Laboratory', '517', '524', '526', '528', '530', '540', '544'];
$purpose_options = [
  'PHP Programming', 'Java Programming', 'ASP.Net Programming', 'C Programming',
  'Python Programming', 'Web Development', 'Mobile App Development',
  'Database Systems', 'Network Security', 'System Administration', 'Capstone Project'
];

$selected_lab = $_GET['lab'] ?? '';
$pcs = [];
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {
    $lab_number = $_POST['lab_number'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $time_in = $_POST['time_in'] ?? '';
    $date = $_POST['date'] ?? '';
    $selected_pc = $_POST['selected_pc'] ?? '';

    if (empty($selected_pc)) {
        $_SESSION['notification_message'] = "You must select a PC before submitting.";
        $_SESSION['notification_type'] = "error";
        header("Location: reservation.php?lab=" . urlencode($lab_number));
        exit();
    }

    if (!empty($lab_number) && !empty($purpose) && !empty($time_in) && !empty($date) && !empty($selected_pc)) {
        $stmt = $conn->prepare("INSERT INTO reservations (id_no, name, lab_number, purpose, date, time_in, selected_pc, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("sssssss", $id_no, $full_name, $lab_number, $purpose, $date, $time_in, $selected_pc);
        $stmt->execute();

        $update = $conn->prepare("UPDATE lab_pc_status SET status = 'Used' WHERE lab_number = ? AND pc_number = ?");
        $update->bind_param("ss", $lab_number, $selected_pc);
        $update->execute();

        $_SESSION['notification_message'] = "Reservation submitted successfully!";
        $_SESSION['notification_type'] = "success";
        header("Location: reservation.php");
        exit();
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
  <title>Reservation</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; }
    .content { margin-left: 270px; padding-top: 50px; }
    .dashboard-row { display: flex; gap: 20px; flex-wrap: wrap; }
    .form-box, .table-box {
      flex: 1; min-width: 0; padding: 20px;
      background-color: #0d121e; border: 2px solid white; border-radius: 20px;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); max-height: 600px; overflow-y: auto;
    }
    .form-box h3, .table-box h3 {
      text-align: center; font-size: 20px; margin-bottom: 15px;
    }
    form label { display: block; margin: 10px 0 5px; }
    form select, form input[type="date"], form input[type="time"] {
      width: 100%; padding: 10px; border-radius: 20px; border: none; font-size: 14px;
    }
    form button {
      margin-top: 15px; width: 100%; padding: 12px; background-color: #212b40;
      color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 15px;
    }
    .student-info {
      font-size: 14px; background: #161d31; padding: 15px;
      border-radius: 10px; margin-bottom: 20px;
    }
    .student-info span {
      font-weight: bold; display: inline-block; min-width: 140px;
    }
    .pc-grid {
      display: grid; grid-template-columns: repeat(5, 1fr);
      gap: 12px; padding-top: 10px;
    }
    .pc-tile {
      border-radius: 15px; text-align: center;
      padding: 12px; cursor: pointer; font-weight: bold;
    }
    .available { background-color: #0d121e; color: white; border: 2px solid #34d399; }
    .used { background-color: #0d121e; color: white; border: 2px solid #f87171; }
    .legend {
      display: flex; justify-content: space-between; margin-top: 20px; font-size: 14px;
    }
    .legend span { display: flex; align-items: center; gap: 8px; }
    .legend-box { width: 16px; height: 16px; border-radius: 4px; }
    .box-available { background-color: #34d399; }
    .box-used { background-color: #f87171; }
    .pc-tile.selected { box-shadow: 0 0 0 3px #22d3ee; }
    .pc-tile.clickable:hover { background-color: #1a2333; }

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
  </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="content">
  <div class="dashboard-row">
    <form method="POST" id="reservationForm" class="form-box">
      <h3><i class="fas fa-calendar-plus"></i> Submit a Reservation</h3>

      <div class="student-info">
        <p><span>ID Number:</span> <?= htmlspecialchars($id_no) ?></p>
        <p><span>Full Name:</span> <?= htmlspecialchars($full_name) ?></p>
        <p><span>Remaining Sessions:</span> <?= htmlspecialchars($remaining_session) ?></p>
      </div>

      <label for="lab_number">Lab Number:</label>
      <select name="lab_number" onchange="window.location.href='?lab=' + this.value" required>
        <option value="">Select Lab</option>
        <?php foreach ($lab_options as $lab): ?>
          <option value="<?= htmlspecialchars($lab) ?>" <?= $lab === $selected_lab ? 'selected' : '' ?>>
            <?= htmlspecialchars($lab) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="purpose">Purpose:</label>
      <select name="purpose" required>
        <option value="">Select Purpose</option>
        <?php foreach ($purpose_options as $purpose): ?>
          <option value="<?= htmlspecialchars($purpose) ?>"><?= htmlspecialchars($purpose) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="time_in">Time In:</label>
      <input type="time" name="time_in" required />

      <label for="date">Date:</label>
      <input type="date" name="date" required />

      <input type="hidden" name="selected_pc" id="selected_pc" required>

      <button type="submit" name="submit_reservation">Reserve</button>
    </form>

    <div class="table-box">
      <h3><i class="fas fa-desktop"></i> Select a PC</h3>
      <?php if (!empty($pcs)): ?>
        <div class="pc-grid" id="pcGrid">
          <?php foreach ($pcs as $pc): ?>
            <?php $disabled = $pc['status'] === 'Used' ? 'disabled' : ''; ?>
            <label>
              <input type="radio" name="pc_choice" value="<?= $pc['pc_number'] ?>" <?= $disabled ?> hidden>
              <div class="pc-tile <?= strtolower($pc['status']) ?> <?= $disabled ? '' : 'clickable' ?>">
                <i class="fas fa-desktop"></i><br><?= $pc['pc_number'] ?><br><small><?= strtoupper($pc['status']) ?></small>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
        <div class="legend">
          <span><div class="legend-box box-available"></div> Available</span>
          <span><div class="legend-box box-used"></div> Used</span>
        </div>
      <?php else: ?>
        <p style="text-align:center; color: #888; margin-top: 80px;">Select a lab to view available PCs.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<div id="notification" class="notification <?= $notification_type ?>">
  <i class="<?= $notification_type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle' ?>"></i>
  <span><?= $notification_message ?></span>
</div>

<script>
  document.querySelectorAll('input[name="pc_choice"]').forEach(input => {
    input.addEventListener('change', () => {
      document.querySelectorAll('.pc-tile').forEach(tile => tile.classList.remove('selected'));
      const selectedTile = input.nextElementSibling;
      selectedTile.classList.add('selected');
      document.getElementById('selected_pc').value = input.value;
    });
  });

  const msg = <?= json_encode($notification_message) ?>;
  const box = document.getElementById('notification');
  if (msg) {
    box.classList.add('show');
    setTimeout(() => {
      box.classList.remove('show');
    }, 3000);
  }
</script>
</body>
</html>
