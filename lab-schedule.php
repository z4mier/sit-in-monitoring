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

$schedules = [];
$query = "SELECT * FROM lab_schedule ORDER BY FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), time_in";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lab Schedule</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #0d121e;
      color: white;
    }
    .content {
      margin-left: 270px;
      padding-top: 50px;
      padding-right: 20px;
      padding-left: 20px;
    }
    h1 {
      font-size: 28px;
      border-bottom: 2px solid #333;
      padding-bottom: 15px;
      margin-bottom: 25px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 14px;
      text-align: center;
    }
    tr:nth-child(even) { background-color: #111524; }
    tr:nth-child(odd) { background-color: #212b40; }
    tr:hover { background-color: #181a25; }
    thead tr { background-color: transparent !important; }
    .pagination-wrapper {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 15px;
      padding-top: 15px;
      font-size: 14px;
    }
    .pagination-wrapper select {
      background-color: #212b40;
      color: white;
      border: 1px solid #555;
      border-radius: 4px;
      padding: 5px 8px;
    }
    .nav-buttons button {
      background-color: #212b40;
      color: white;
      border: none;
      padding: 6px 10px;
      margin-left: 5px;
      cursor: pointer;
      font-size: 16px;
      border-radius: 4px;
    }
    .nav-buttons button:hover {
      background-color: #2e3b5e;
    }
  </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="content">
  <h1>Laboratory Schedule</h1>

  <table>
    <thead>
      <tr>
        <th>Time Slot</th>
        <th>Day</th>
        <th>Lab #</th>
        <th>Subject</th>
        <th>Professor</th>
      </tr>
    </thead>
    <tbody id="scheduleTable">
      <?php if (count($schedules) > 0): ?>
        <?php foreach ($schedules as $sched): ?>
          <tr class="schedule-row">
            <td><?= date("g:i A", strtotime($sched['time_in'])) ?> - <?= date("g:i A", strtotime($sched['time_out'])) ?></td>
            <td><?= htmlspecialchars($sched['day']) ?></td>
            <td><?= htmlspecialchars($sched['lab']) ?></td>
            <td><?= htmlspecialchars($sched['subject']) ?></td>
            <td><?= htmlspecialchars($sched['professor']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" style="text-align: center; padding: 20px; color: #ccc;">No schedules available.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination-wrapper">
    <div class="rows-selector">
      Rows per page:
      <select id="rowsPerPage" onchange="updatePagination()">
        <option value="5">5</option>
        <option value="10" selected>10</option>
        <option value="20">20</option>
      </select>
    </div>
    <div id="pageInfo"></div>
    <div class="nav-buttons">
      <button onclick="goToFirstPage()">«</button>
      <button onclick="goToPreviousPage()">‹</button>
      <button onclick="goToNextPage()">›</button>
      <button onclick="goToLastPage()">»</button>
    </div>
  </div>
</div>

<script>
let currentPage = 1;
let rowsPerPage = 10;
const table = document.getElementById("scheduleTable");
const allRows = Array.from(table.querySelectorAll(".schedule-row"));
const pageInfo = document.getElementById("pageInfo");

function updatePagination() {
  rowsPerPage = parseInt(document.getElementById("rowsPerPage").value);
  currentPage = 1;
  displayPage();
}

function displayPage() {
  table.innerHTML = '';
  const totalPages = Math.ceil(allRows.length / rowsPerPage);
  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const visibleRows = allRows.slice(start, end);

  if (visibleRows.length === 0) {
    table.innerHTML = "<tr><td colspan='5' style='text-align:center; padding: 20px; color: #ccc;'>No schedules available.</td></tr>";
  } else {
    visibleRows.forEach(row => table.appendChild(row));
  }

  pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;
}

function goToFirstPage() { currentPage = 1; displayPage(); }
function goToPreviousPage() { if (currentPage > 1) currentPage--; displayPage(); }
function goToNextPage() {
  const totalPages = Math.ceil(allRows.length / rowsPerPage);
  if (currentPage < totalPages) currentPage++;
  displayPage();
}
function goToLastPage() {
  currentPage = Math.ceil(allRows.length / rowsPerPage);
  displayPage();
}

window.onload = displayPage;
</script>
</body>
</html>
