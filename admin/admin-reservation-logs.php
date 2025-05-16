<?php
session_start();
include '../includes/db-connection.php';

$log_rows = '';
$query = "
  SELECT 
    r.id_no, 
    u.firstname, 
    u.middlename, 
    u.lastname, 
    r.date, 
    r.time_in, 
    r.lab_number, 
    r.selected_pc,
    r.status
  FROM reservations r
  LEFT JOIN users u ON r.id_no = u.id_no
  WHERE r.status IN ('Approved', 'Rejected')
  ORDER BY r.status_updated_at DESC
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $full_name = htmlspecialchars(trim(
            $row['firstname'] . ' ' .
            (!empty($row['middlename']) ? $row['middlename'] . ' ' : '') .
            $row['lastname']
        ));

        $log_rows .= "<tr>
            <td>" . htmlspecialchars($row['id_no']) . "</td>
            <td>$full_name</td>
            <td>" . (!empty($row['date']) ? htmlspecialchars($row['date']) : '—') . "</td>
            <td>" . (!empty($row['time_in']) ? htmlspecialchars($row['time_in']) : '—') . "</td>
            <td>" . htmlspecialchars($row['lab_number']) . "</td>
            <td>" . htmlspecialchars($row['selected_pc']) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>
        </tr>";
    }
} else {
    $log_rows = "<tr><td colspan='7' style='text-align: center;'>No reservation logs found.</td></tr>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservation Logs</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; display: flex; }
    .main-content { margin-left: 80px; padding: 20px; flex: 1; }
    .sidebar:hover ~ .main-content { margin-left: 180px; }
    h1 { font-size: 28px; margin-bottom: 10px; }
    .back-btn {
      padding: 8px 16px;
      background-color: white;
      color: black;
      border-radius: 15px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .table-container { margin-top: 10px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; text-align: center; }
    thead tr { background-color: transparent !important; }
    table tr:nth-child(even) { background-color: #111524; }
    table tr:nth-child(odd) { background-color: #212b40; }
    table tr:hover { background-color: #181a25; }
    hr {
      border: none;
      border-top: 2px solid #333;
      margin: 10px 0 15px;
    }
    .pagination-wrapper {
      display: flex; justify-content: flex-end; align-items: center; gap: 15px;
      padding-top: 15px; font-size: 14px;
    }
    .pagination-wrapper select {
      background-color: #212b40; color: white;
      border: 1px solid #555; border-radius: 4px;
      padding: 5px 8px;
    }
    .nav-buttons button {
      background-color: #212b40; color: white;
      border: none; padding: 6px 10px; margin-left: 5px;
      cursor: pointer; font-size: 16px; border-radius: 4px;
    }
    .nav-buttons button:hover {
      background-color: #2e3b5e;
    }
  </style>
</head>
<body>
<?php include '../includes/admin-sidebar.php'; ?>
<div class="main-content">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Admin - Reservation Logs</h1>
    <a href="admin-reservations.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
  <hr>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID Number</th>
          <th>Name</th>
          <th>Date</th>
          <th>Time</th>
          <th>Lab</th>
          <th>PC</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="reservationLogTable">
        <?= $log_rows ?>
      </tbody>
    </table>
  </div>

  <div class="pagination-wrapper">
    <div class="rows-selector">
      Rows per page:
      <select id="rowsPerPage" onchange="updatePagination()">
        <option value="5">5</option>
        <option value="10" selected>10</option>
        <option value="20">20</option>
        <option value="50">50</option>
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
const table = document.getElementById("reservationLogTable");
const allRows = Array.from(table.querySelectorAll("tr"));
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
  visibleRows.forEach(row => table.appendChild(row));
  pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;
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
