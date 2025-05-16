<?php
session_start();
include '../includes/db-connection.php';

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
  <title>Admin - Laboratory Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #0d121e;
      color: white;
      display: flex;
    }
    .main-content {
      margin-left: 80px;
      padding: 20px;
      flex: 1;
    }
    .sidebar:hover ~ .main-content {
      margin-left: 180px;
    }
    h1 {
      font-size: 28px;
      margin-bottom: 20px;
    }
    .add-btn {
      background-color: white;
      color: black;
      padding: 10px 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .add-btn:hover {
      background-color: #ddd;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 15px;
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

    .icon-btn {
      background: none;
      border: none;
      color: white;
      font-size: 16px;
      margin: 0 5px;
      cursor: pointer;
    }
    .icon-btn.edit:hover { color: #3b82f6; }
    .icon-btn.delete:hover { color: #ef4444; }

    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .modal {
      background-color: #0d121e;
      color: white;
      padding: 30px;
      border-radius: 12px;
      width: 480px;
      max-width: 90%;
      position: relative;
    }
    .modal h2 {
      margin-bottom: 20px;
      text-align: center;
    }
    .modal label {
      display: block;
      font-weight: 500;
      margin: 15px 0 6px;
    }
    .modal input,
    .modal select {
    width: 100%;
    padding: 12px 14px;
    font-size: 14px;
    line-height: 1.5;
    border-radius: 6px;
    background-color: #1f2937;
    color: white;
    border: none;
    box-sizing: border-box;
    appearance: none; 
    height: 48px; 
    }
    input[type="time"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    }
    .modal button {
      padding: 10px 16px;
      border-radius: 6px;
      font-size: 14px;
      border: none;
      cursor: pointer;
    }
    .modal .cancel-btn {
      background-color: #f44336;
      color: white;
    }
    .modal .save-btn {
      background-color: #007bff;
      color: white;
    }
    .modal .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      background: #0d121e;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 5px 10px;
      cursor: pointer;
      font-size: 20px;
    }
  </style>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
   <div style="padding-bottom: 20px; border-bottom: 2px solid #333; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h1>Laboratory Management</h1>
    <button class="add-btn" onclick="document.getElementById('scheduleModal').style.display='flex'">
      <i class="fas fa-calendar-plus"></i> Add Schedule
    </button>
  </div>    
  <table>
    <thead>
      <tr>
        <th>Time Slot</th>
        <th>Day</th>
        <th>Lab #</th>
        <th>Subject</th>
        <th>Professor</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="scheduleTable">
      <?php if (count($schedules) > 0): ?>
        <?php foreach ($schedules as $sched): ?>
          <tr class="schedule-row">
            <td>
            <?= date("g:i A", strtotime($sched['time_in'])) ?> - 
            <?= date("g:i A", strtotime($sched['time_out'])) ?>
            </td>
            <td><?= htmlspecialchars($sched['day']) ?></td>
            <td><?= htmlspecialchars($sched['lab']) ?></td>
            <td><?= htmlspecialchars($sched['subject']) ?></td>
            <td><?= htmlspecialchars($sched['professor']) ?></td>
            <td>
              <button class="icon-btn edit" title="Edit"
                onclick='openEditModal(
                    <?= json_encode($sched["id"]) ?>,
                    <?= json_encode($sched["day"]) ?>,
                    <?= json_encode($sched["lab"]) ?>,
                    <?= json_encode($sched["time_in"]) ?>,
                    <?= json_encode($sched["time_out"]) ?>,
                    <?= json_encode($sched["subject"]) ?>,
                    <?= json_encode($sched["professor"]) ?>
                )'>
                <i class="fas fa-edit"></i>
                </button>
              <form method="POST" action="../includes/delete-lab-schedule.php" style="display:inline;">
                <input type="hidden" name="schedule_id" value="<?= $sched['id'] ?>">
                <button class="icon-btn delete" title="Delete" onclick="return confirm('Delete this schedule?')"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align: center; padding: 20px; color: #ccc;">No schedules added yet.</td></tr>
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

<!-- Add Schedule Modal -->
<div class="modal-overlay" id="scheduleModal">
  <div class="modal">
    <button class="close-btn" onclick="document.getElementById('scheduleModal').style.display='none'">×</button>
    <h2>Add Laboratory Schedule</h2>
    <form action="../includes/save-lab-schedule.php" method="POST">
      <label>Day of Week</label>
      <select name="day" required>
        <option value="">Select Day</option>
        <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
          <option value="<?= $day ?>"><?= $day ?></option>
        <?php endforeach; ?>
      </select>

      <label>Laboratory</label>
      <select name="lab" required>
        <option value="">Select Laboratory</option>
        <?php foreach (['Mac Laboratory','517','524','526','528','530','540','544'] as $lab): ?>
          <option value="<?= $lab ?>"><?= $lab ?></option>
        <?php endforeach; ?>
      </select>

      <label>Time In</label>
      <input type="time" name="time_in" required />

      <label>Time Out</label>
      <input type="time" name="time_out" required />

      <label>Subject</label>
      <input type="text" name="subject" required placeholder="Enter subject name" />

      <label>Professor</label>
      <input type="text" name="professor" required placeholder="Enter professor name" />

      <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
        <button type="button" class="cancel-btn" onclick="document.getElementById('scheduleModal').style.display='none'">Cancel</button>
        <button type="submit" class="save-btn">Save Schedule</button>
      </div>
    </form>
  </div>
</div>
<!-- Edit Schedule Modal -->
<div class="modal-overlay" id="editScheduleModal" style="display: none;">
  <div class="modal">
    <button class="close-btn" onclick="document.getElementById('editScheduleModal').style.display='none'">×</button>
    <h2>Edit Laboratory Schedule</h2>
    <form action="../includes/update-lab-schedule.php" method="POST">
      <input type="hidden" name="id" id="edit-id" />

      <label>Day of Week</label>
      <select name="day" id="edit-day" required>
        <option value="">Select Day</option>
        <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
          <option value="<?= $day ?>"><?= $day ?></option>
        <?php endforeach; ?>
      </select>

      <label>Laboratory</label>
      <select name="lab" id="edit-lab" required>
        <option value="">Select Laboratory</option>
        <?php foreach (['Mac Laboratory','517','524','526','528','530','540','544'] as $lab): ?>
          <option value="<?= $lab ?>"><?= $lab ?></option>
        <?php endforeach; ?>
      </select>

      <label>Time In</label>
      <input type="time" name="time_in" id="edit-time-in" required />

      <label>Time Out</label>
      <input type="time" name="time_out" id="edit-time-out" required />

      <label>Subject</label>
      <input type="text" name="subject" id="edit-subject" required placeholder="Enter subject name" />

      <label>Professor</label>
      <input type="text" name="professor" id="edit-professor" required placeholder="Enter professor name" />

      <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
        <button type="button" class="cancel-btn" onclick="document.getElementById('editScheduleModal').style.display='none'">Cancel</button>
        <button type="submit" class="save-btn">Update Schedule</button>
      </div>
    </form>
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
    table.innerHTML = "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #ccc;'>No schedules added yet.</td></tr>";
  } else {
    visibleRows.forEach(row => table.appendChild(row));
  }

  pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;
}
function openEditModal(id, day, lab, timeIn, timeOut, subject, professor) {
  document.getElementById("edit-id").value = id;
  document.getElementById("edit-day").value = day;
  document.getElementById("edit-lab").value = lab;
  document.getElementById("edit-time-in").value = timeIn;
  document.getElementById("edit-time-out").value = timeOut;
  document.getElementById("edit-subject").value = subject;
  document.getElementById("edit-professor").value = professor;

  document.getElementById("editScheduleModal").style.display = "flex";
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
