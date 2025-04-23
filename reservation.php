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


$lab_options = ['Mac Laboratory', '540', '530', '526'];
$purpose_options = ['PHP Programming', 'Java Programming', 'ASP.Net Programming', 'C Programming'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab_number = $_POST['lab_number'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!empty($lab_number) && !empty($purpose) && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO reservations (id_no, lab_number, purpose, date, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssss", $id_no, $lab_number, $purpose, $date);
        $stmt->execute();
    }
}

$reservations = [];
$stmt = $conn->prepare("SELECT * FROM reservations WHERE id_no = ? ORDER BY date DESC");
$stmt->bind_param("s", $id_no);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
      .content { margin-left: 270px; padding-top: 150px; }
      .dashboard-row { display: flex; gap: 20px; flex-wrap: wrap; }
      form input[type="date"] { -webkit-appearance: none; appearance: none; box-sizing: border-box; }
      .form-box, .table-box { flex: 1; min-width: 0; padding: 20px; background-color: #0d121e; border: 2px solid white; border-radius: 20px; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); max-height: 600px; overflow-y: auto; }
      .form-box h3, .table-box h3 { text-align: center; font-size: 20px; margin-bottom: 15px; }
      .form-box h3 i, .table-box h3 i { margin-right: 10px; }
      form label { display: block; margin: 10px 0 5px; }
      form select, form input[type="date"] { width: 100%; padding: 10px; border-radius: 20px; border: none; font-size: 14px; }
      form button { margin-top: 15px; width: 100%; padding: 12px; background-color: #212b40; color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 15px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 12px; text-align: center; }
      tr:nth-child(even) { background-color: #111524; }
      tr:nth-child(odd) { background-color: #212b40; }
      .status { padding: 5px 10px; border-radius: 5px; }
      thead tr { background-color: transparent !important; }
      .Pending, .Approved, .Rejected { color: white; }
  </style>

</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="content">
  <div class="dashboard-row">
    <div class="form-box">
      <h3><i class="fas fa-calendar-plus"></i> Submit a Reservation</h3>
      <form method="POST">
        <label for="lab_number">Lab Number:</label>
        <select name="lab_number" required>
          <option value="">Select Lab</option>
          <?php foreach ($lab_options as $lab): ?>
            <option value="<?= htmlspecialchars($lab) ?>"><?= htmlspecialchars($lab) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="purpose">Purpose:</label>
        <select name="purpose" required>
          <option value="">Select Purpose</option>
          <?php foreach ($purpose_options as $purpose): ?>
            <option value="<?= htmlspecialchars($purpose) ?>"><?= htmlspecialchars($purpose) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="date">Date:</label>
        <input type="date" name="date" required />

        <button type="submit">Reserve</button>
      </form>
    </div>

    <div class="table-box">
      <h3><i class="fas fa-table"></i> My Reservations</h3>
      <table>
        <thead>
          <tr>
            <th>Lab</th>
            <th>Purpose</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($reservations)): ?>
            <?php foreach ($reservations as $res): ?>
              <tr>
                <td><?= htmlspecialchars($res['lab_number']) ?></td>
                <td><?= htmlspecialchars($res['purpose']) ?></td>
                <td><?= htmlspecialchars($res['date']) ?></td>
                <td><span class="status <?= $res['status'] ?>"><?= $res['status'] ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4">No reservations found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
