<?php
session_start();
include '../includes/db-connection.php';

$sit_in_rows = '';
$sort_column = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'DESC';

$allowed_columns = ['id', 'id_no', 'name', 'purpose', 'lab_number', 'time_in', 'time_out', 'date'];
$allowed_orders = ['ASC', 'DESC'];
if (!in_array($sort_column, $allowed_columns)) $sort_column = 'time_in';
if (!in_array($sort_order, $allowed_orders)) $sort_order = 'DESC';

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$lab_filter = $_GET['lab_filter'] ?? '';
$purpose_filter = $_GET['purpose_filter'] ?? '';
$search = $_GET['search'] ?? '';

$filters = [];

if ($start_date && $end_date) {
    $filters[] = "date BETWEEN '$start_date' AND '$end_date'";
} elseif ($start_date) {
    $filters[] = "date >= '$start_date'";
} elseif ($end_date) {
    $filters[] = "date <= '$end_date'";
}

if (!empty($lab_filter)) {
    $filters[] = "lab_number = '$lab_filter'";
}

if (!empty($purpose_filter)) {
    $filters[] = "purpose = '$purpose_filter'";
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $filters[] = "(id_no LIKE '%$search%' OR name LIKE '%$search%' OR purpose LIKE '%$search%' OR lab_number LIKE '%$search%')";
}

$where_clause = count($filters) ? "WHERE " . implode(" AND ", $filters) : "";

$sql = "SELECT r.id, r.id_no, r.name, r.purpose, r.lab_number, r.time_in, r.time_out, r.date
        FROM sit_in_records r
        $where_clause
        ORDER BY $sort_column $sort_order";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sit_in_rows .= "<tr>
            <td>" . htmlspecialchars($row['id_no']) . "</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['purpose']) . "</td>
            <td>" . htmlspecialchars($row['lab_number']) . "</td>
            <td>" . ($row['time_in'] ? date('H:i:s', strtotime($row['time_in'])) : '—') . "</td>
            <td>" . ($row['time_out'] ? date('H:i:s', strtotime($row['time_out'])) : '—') . "</td>
            <td>" . htmlspecialchars($row['date']) . "</td>
        </tr>";
    }
} else {
    $sit_in_rows = "<tr><td colspan='7'>No sit-in records found.</td></tr>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Sit-In Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<style>
      body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: #ffffff; display: flex; }
      .main-content { margin-left: 80px; padding: 20px; flex: 1; }
      .sidebar:hover ~ .main-content { margin-left: 180px; }
      header { display: flex; justify-content: space-between; align-items: center; padding: 20px; }
      h1 { margin: 0; font-size: 28px; }
      .search-container { position: relative; display: flex; align-items: center; }
      .search-container input[type="text"] { padding: 10px 40px 10px 15px; border-radius: 25px; width: 240px; background-color: white; color: black; border: 2px solid #ffffff; }
      .search-container .search-icon { position: absolute; right: 15px; color: black; pointer-events: none; }
      .top-controls { display: flex; justify-content: space-between; align-items: center; margin: 0 20px 10px; gap: 10px; }
      .filter-tools { display: flex; flex-wrap: wrap; gap: 10px; }
      .filter-tools select, .filter-tools a { padding: 6px 12px; font-size: 14px; border: none; border-radius: 5px; }
      .filter-tools a { background-color: white; color: black; text-decoration: none; display: flex; align-items: center; }
      .export-buttons { display: flex; gap: 15px; }
      .export-buttons button { background-color: #f1f1f1; border: none; padding: 8px; border-radius: 5px; cursor: pointer; color: #111; }
      .export-buttons button i { font-size: 20px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 12px; text-align: center; }
      thead tr { background-color: transparent !important; }
      tbody tr:nth-child(even) { background-color: #111524; }
      tbody tr:nth-child(odd) { background-color: #212b40; }
      tbody tr:hover { background-color: #181a25; }
      .pagination-wrapper { display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding-top: 15px; font-size: 14px; }
      .pagination-wrapper select { background-color: #212b40; color: white; border: 1px solid #555; border-radius: 4px; padding: 5px 8px; }
      .nav-buttons button { background-color: #212b40; color: white; border: none; padding: 6px 10px; margin-left: 5px; cursor: pointer; font-size: 16px; border-radius: 4px; }
      .nav-buttons button:hover { background-color: #2e3b5e; }
</style>

</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
  <header>
    <h1>Admin - Sit-In Reports</h1>
    <form class="search-container" method="GET" action="" id="searchForm">
      <input type="text" name="search" id="searchInput" placeholder="Search" value="<?= htmlspecialchars($search) ?>" 
             oninput="handleSearchInput(this)">
      <i class="fas fa-search search-icon"></i>

      <?php if (!empty($lab_filter)): ?>
        <input type="hidden" name="lab_filter" value="<?= htmlspecialchars($lab_filter) ?>">
      <?php endif; ?>
      <?php if (!empty($purpose_filter)): ?>
        <input type="hidden" name="purpose_filter" value="<?= htmlspecialchars($purpose_filter) ?>">
      <?php endif; ?>
      <?php if (!empty($start_date)): ?>
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
      <?php endif; ?>
      <?php if (!empty($end_date)): ?>
        <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
      <?php endif; ?>
    </form>
  </header>

  <div class="top-controls">
    <form class="filter-tools" method="GET" id="autoFilterForm">
      <select name="lab_filter" onchange="this.form.submit();">
        <option value="">Filter Lab</option>
        <?php foreach (['524','526','528','530','540','Mac Laboratory'] as $lab): ?>
          <option value="<?= $lab ?>" <?= $lab_filter === $lab ? 'selected' : '' ?>><?= $lab ?></option>
        <?php endforeach; ?>
      </select>

      <select name="purpose_filter" onchange="this.form.submit();">
        <option value="">Filter Purposes</option>
        <?php foreach (['PHP Programming','Java Programming','ASP.Net Programming','C Programming','C# Programming'] as $purpose): ?>
          <option value="<?= $purpose ?>" <?= $purpose_filter === $purpose ? 'selected' : '' ?>><?= $purpose ?></option>
        <?php endforeach; ?>
      </select>

      <?php if (!empty($search)): ?>
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
      <?php endif; ?>

      <a href="admin-reports.php" title="Reset Filters"><i class="fas fa-sync-alt"></i></a>
    </form>

    <div class="export-buttons">
      <button onclick="exportTable('csv')" title="Export CSV"><i class="fas fa-file-csv"></i></button>
      <button onclick="exportTable('excel')" title="Export Excel"><i class="fas fa-file-excel"></i></button>
      <button onclick="exportTable('pdf')" title="Export PDF"><i class="fas fa-file-pdf"></i></button>
      <button onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
    </div>
  </div>

  <hr style="border: none; border-top: 2px solid #333; margin: 10px 20px 15px;">

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
        </tr>
      </thead>
      <tbody id="sitInTable">
        <?= $sit_in_rows ?>
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

function exportTable(type) {
  const table = document.getElementById("sitInTable");
  const rows = [...table.rows].map(row =>
    [...row.cells].map(cell => cell.innerText)
  );
  const headers = ["ID Number", "Name", "Purpose", "Lab", "Time In", "Time Out", "Date"];

  if (type === "csv" || type === "excel") {
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);
    XLSX.utils.book_append_sheet(wb, ws, "SitInRecords");

    if (type === "csv") {
      XLSX.writeFile(wb, "SitInRecords.csv");
    } else {
      XLSX.writeFile(wb, "SitInRecords.xlsx");
    }
  } else if (type === "pdf") {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const ucImg = new Image();
    ucImg.src = '../assets/uc.png';

    const ccsImg = new Image();
    ccsImg.src = '../assets/ccs.png';

    const now = new Date();
    const printedDate = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

    ucImg.onload = () => {
      ccsImg.onload = () => {
        doc.addImage(ucImg, 'PNG', 15, 10, 20, 20);
        doc.addImage(ccsImg, 'PNG', 175, 10, 20, 20);

        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text("University of Cebu-Main Campus", 105, 15, { align: "center" });
        doc.setFont("helvetica", "normal");
        doc.text("College of Computer Studies", 105, 22, { align: "center" });
        doc.text("CCS Sit-in Monitoring System Reports", 105, 29, { align: "center" });

        doc.setFontSize(10);
        doc.text(`Printed Date: ${printedDate}`, 15, 40);

        doc.autoTable({
          startY: 45,
          head: [headers],
          body: rows,
          theme: "grid",
          styles: { fontSize: 9 },
          headStyles: { fillColor: [30, 30, 30], halign: "center" },
          bodyStyles: { halign: "center" },
        });

        doc.save("SitInRecords.pdf");
      };
    };
  }
}
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