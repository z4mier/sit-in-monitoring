<?php
session_start();
include '../includes/db-connection.php';

$sit_in_rows = '';
$notification_message = $_SESSION['notification_message'] ?? '';
$notification_type = $_SESSION['notification_type'] ?? '';
unset($_SESSION['notification_message'], $_SESSION['notification_type']);

$sort_column = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'DESC';

$allowed_columns = ['id', 'id_no', 'name', 'purpose', 'lab_number', 'time_in', 'time_out', 'date', 'points'];
$allowed_orders = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) $sort_column = 'time_in';
if (!in_array($sort_order, $allowed_orders)) $sort_order = 'DESC';

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$search = $_GET['search'] ?? '';

$filters = [];

if ($start_date && $end_date) {
    $filters[] = "date BETWEEN '$start_date' AND '$end_date'";
} elseif ($start_date) {
    $filters[] = "date >= '$start_date'";
} elseif ($end_date) {
    $filters[] = "date <= '$end_date'";
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $filters[] = "(id_no LIKE '%$search%' OR name LIKE '%$search%' OR purpose LIKE '%$search%' OR lab_number LIKE '%$search%')";
}

$where_clause = count($filters) ? "WHERE " . implode(" AND ", $filters) : "";

$sql = "SELECT r.id, r.id_no, r.name, r.purpose, r.lab_number, r.time_in, r.time_out, r.date, r.points, u.remaining_sessions
        FROM sit_in_records r
        JOIN users u ON r.id_no = u.id_no
        $where_clause
        ORDER BY $sort_column $sort_order";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id']);
        $id_no = htmlspecialchars($row['id_no']);
        $name = htmlspecialchars($row['name']);
        $purpose = htmlspecialchars($row['purpose']);
        $lab_number = htmlspecialchars($row['lab_number']);
        $time_in = $row['time_in'] ? date('H:i:s', strtotime($row['time_in'])) : '—';
        $time_out = $row['time_out'] ? date('H:i:s', strtotime($row['time_out'])) : '—';
        $date = htmlspecialchars($row['date']);
        $points = (int)($row['points'] ?? 0);
        $remaining_sessions = (int)$row['remaining_sessions'];

        $disabled = $remaining_sessions >= 30 ? "disabled" : "";
        $messageText = $remaining_sessions >= 30 ? "<span style='color: #f87171; font-size: 13px;'>Student has reached the maximum sessions</span>" : "";

        $sit_in_rows .= "
        <tr>
            <td>$id_no</td>
            <td>$name</td>
            <td>$purpose</td>
            <td>$lab_number</td>
            <td>$time_in</td>
            <td>$time_out</td>
            <td>$date</td>
            <td>
                <div style='display: flex; flex-direction: column; align-items: center; gap: 6px;'>
                    <span style='font-size: 14px;'>Given: <strong>$points</strong></span>
                    <form method='POST' action='../includes/assign-points.php' style='display: flex; justify-content: center; align-items: center; gap: 6px;'>
                        <input type='hidden' name='sit_in_id' value='$id'>
                        <input 
                            type='number' 
                            name='points' 
                            min='0' 
                            max='1' 
                            oninput='this.value = Math.max(0, Math.min(1, this.value));'
                            style='width: 50px; padding: 6px; text-align: center; font-size: 14px;' 
                            $disabled
                        />
                        <button type='submit' style='border: none; background-color: white; color: #111524; border-radius: 5px; padding: 6px 10px; cursor: pointer;' title='Add Point' $disabled>
                            <i class='fas fa-plus'></i>
                        </button>
                    </form>
                </div>
            </td>
            <td>$messageText</td>
        </tr>";
    }
} else {
    $sit_in_rows = "<tr><td colspan='10'>No sit-in records found.</td></tr>";
}

$conn->close();
?>

<?php if (!empty($notification_message)): ?>
<div style="
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: <?= $notification_type === 'success' ? '#212b40' : '#f44336' ?>;
    border: 2px solid white;
    color: white;
    padding: 12px 25px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 0 12px rgba(0,0,0,0.4);
">
    <i class="<?= $notification_type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle' ?>" style="color: white;"></i>
    <span><?= $notification_message ?></span>
</div>

<script>
  setTimeout(() => {
    const notif = document.querySelector('div[style*="position: fixed"]');
    if (notif) {
      notif.style.transition = "opacity 0.6s";
      notif.style.opacity = "0";
      setTimeout(() => notif.remove(), 600);
    }
  }, 3000); // Auto-hide after 3 seconds
</script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Sit-In Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; display: flex; background-color: #0d121e; color: #ffffff; }
        .main-content { margin-left: 80px; padding: 20px; flex: 1; }
        .sidebar:hover ~ .main-content { margin-left: 180px; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 2px solid #333; }
        .table-container { margin-top: 20px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 15px; text-align: center; }
        thead tr { background-color: transparent !important; }
        table tr:nth-child(even) { background-color: #111524; }
        table tr:nth-child(odd) { background-color: #212b40; }
        table tr:hover { background-color: #181a25; }
        .search-container { display: flex; align-items: center; margin-bottom: 20px; position: relative; }
        .search-container input { padding: 10px 10px 10px 30px; border: none; border-radius: 20px; width: 200px; background-color: white; color: black; }
        .search-container .search-icon { position: absolute; left: 10px; color: black; }
        .sort-arrow { margin-left: 5px; transition: transform 0.3s; }
        .sort-arrow.asc { transform: rotate(180deg); }
        td:nth-child(1) { text-align: left; padding-left: 70px; }
        .pagination-wrapper { display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding: 15px 20px 0 0; color: white; font-size: 14px; }
        .pagination-wrapper select { background-color: #212b40; color: white; border: 1px solid #555; border-radius: 4px; padding: 5px 8px; }
        .nav-buttons button { background-color: #212b40; color: white; border: none; padding: 6px 10px; margin-left: 5px; cursor: pointer; font-size: 16px; border-radius: 4px; }
        .nav-buttons button:hover { background-color: #2e3b5e; }
    </style>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
<header>
    <h1>Sit-In Records</h1>
    <form class="search-container" method="GET" action="" id="searchForm">
        <input type="text" name="search" id="searchInput" placeholder="Search" value="<?= htmlspecialchars($search) ?>" 
               oninput="handleSearchInput(this)">
        <i class="fas fa-search search-icon"></i>
    </form>
</header>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Lab #</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Date</th>
                <th>Points</th>
                <th>Session Status</th>
            </tr>
            </thead>
            <tbody id="sitInTable">
                <?= $sit_in_rows ?>
            </tbody>
        </table>

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
</div>

<script>
let searchTimeout;

function handleSearchInput(input) {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    document.getElementById('searchForm').submit();
  }, 500);
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    document.getElementById('searchForm').submit();
  }
});

let currentPage = 1;
let rowsPerPage = 10;
const table = document.getElementById("sitInTable");
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
