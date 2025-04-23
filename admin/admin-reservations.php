<?php
session_start();
include '../includes/db-connection.php';

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$sort_order = $_GET['sort'] ?? 'DESC'; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows_per_page = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $rows_per_page;

$where = [];
if (!empty($search)) {
    $escaped = $conn->real_escape_string($search);
    $where[] = "(r.id_no LIKE '%$escaped%' OR CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) LIKE '%$escaped%' OR r.purpose LIKE '%$escaped%' OR r.lab_number LIKE '%$escaped%')";
}
if (!empty($status_filter)) {
    $where[] = "r.status = '" . $conn->real_escape_string($status_filter) . "'";
}
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total_sql = "SELECT COUNT(*) AS total 
              FROM reservations r 
              LEFT JOIN users u ON r.id_no = u.id_no 
              $where_clause";
$total_result = $conn->query($total_sql);
$total_rows = ($total_result && $total_result->num_rows > 0) ? $total_result->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_rows / $rows_per_page);

$sql = "SELECT r.*, CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) AS name 
        FROM reservations r 
        LEFT JOIN users u ON r.id_no = u.id_no 
        $where_clause 
        ORDER BY r.date $sort_order 
        LIMIT $offset, $rows_per_page";

$result = $conn->query($sql);
$reservation_rows = '';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status_class = strtolower($row['status']);
        $reservation_rows .= "
        <tr>
            <td>{$row['id_no']}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['purpose']) . "</td>
            <td>" . htmlspecialchars($row['lab_number']) . "</td>
            <td>" . htmlspecialchars($row['date']) . "</td>
            <td><span class='status $status_class'>{$row['status']}</span></td>
            <td>
                <form method='POST' action='../includes/update-reservation.php' style='display:flex; gap:6px; justify-content:center;'>
                    <input type='hidden' name='reservation_id' value='{$row['id']}'>
                    <button name='action' value='Approved' class='approve-btn'>✔</button>
                    <button name='action' value='Rejected' class='reject-btn'>✖</button>
                </form>
            </td>
        </tr>";
    }
} else {
    $reservation_rows = "<tr><td colspan='7' style='text-align:center;'>No reservations found.</td></tr>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Reservation Approval</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
 
  <style>
      body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; display: flex; }
      .main-content { margin-left: 80px !important; padding: 20px; flex: 1; }
      .sidebar:hover ~ .main-content { margin-left: 180px !important; }
      thead tr { background-color: transparent !important; }
      .custom-toast { background-color: #1f2937; color: white; padding: 14px 20px; border-radius: 10px; font-size: 15px; margin-bottom: 15px; animation: fadeIn 0.3s ease; }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
      header { padding: 20px; display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; border-bottom: 2px solid #333; }
      header h1 { font-size: 28px; margin: 0 0 15px 0; }
      header form { display: flex; gap: 10px; }
      select, input[type="text"] { padding: 10px; border-radius: 6px; font-size: 14px; border: none; background-color: white; color: black; }
      input[type="text"] { width: 200px; }
      .search-bar { display: flex; align-items: center; gap: 10px; margin-top: 5px; }
      .search-bar input { padding: 10px 40px 10px 15px; border-radius: 999px; border: none; width: 250px; background-color: white; color: black; }
      .search-bar button { position: absolute; top: 50%; right: 12px; transform: translateY(-50%); background: none; border: none; cursor: pointer; }
      .search-bar i { color: black; }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      th, td { padding: 15px; text-align: center; }
      table tr:nth-child(even) { background-color: #111524; }
      table tr:nth-child(odd) { background-color: #212b40; }
      table tr:hover { background-color: #181a25; }
      .status { padding: 6px 10px; border-radius: 12px; font-weight: 600; }
      .approved, .pending, .rejected { color: white; }
      .approve-btn, .reject-btn { background-color: transparent; color: white; border: 1px solid #555; padding: 6px 10px; border-radius: 5px; cursor: pointer; }
      .pagination-wrapper { display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding-top: 15px; font-size: 14px; }
      .pagination-wrapper select { background-color: #212b40; color: white; border: 1px solid #555; border-radius: 4px; padding: 5px 8px; }
      .nav-buttons button { background-color: #212b40; color: white; border: none; padding: 6px 10px; margin-left: 5px; cursor: pointer; font-size: 16px; border-radius: 4px; }
      .nav-buttons button:hover { background-color: #2e3b5e; }
  </style>

</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
  <?php if (isset($_SESSION['success'])): ?>
    <div class="custom-toast" id="toast-success">
      <?= $_SESSION['success']; ?>
    </div>
    <script>
      setTimeout(() => {
        const toast = document.getElementById('toast-success');
        if (toast) toast.style.display = 'none';
      }, 3000);
    </script>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <header>
    <div style="flex: 1;">
      <h1>Reservation Approval</h1>
      <form method="GET" id="filterForm">
        <select name="status" onchange="document.getElementById('filterForm').submit()">
          <option value="">Filter Status</option>
          <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Approved" <?= $status_filter === 'Approved' ? 'selected' : '' ?>>Approved</option>
          <option value="Rejected" <?= $status_filter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <select name="sort" onchange="document.getElementById('filterForm').submit()">
        <option value="">Sort by</option>
          <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Newest to Oldest</option>
          <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Oldest to Newest</option>
        </select>
      </form>
    </div>

    <form method="GET" class="search-bar" style="position: relative;">
      <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
      <input type="hidden" name="sort" value="<?= htmlspecialchars($sort_order) ?>">
      <input type="text" name="search" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
      <button type="submit"><i class="fas fa-search"></i></button>
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
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?= $reservation_rows ?>
      </tbody>
    </table>

    <div class="pagination-wrapper">
      <div>
        Rows per page:
        <select onchange="changeLimit(this.value)">
          <option value="5" <?= $rows_per_page == 5 ? 'selected' : '' ?>>5</option>
          <option value="10" <?= $rows_per_page == 10 ? 'selected' : '' ?>>10</option>
          <option value="20" <?= $rows_per_page == 20 ? 'selected' : '' ?>>20</option>
        </select>
      </div>
      <div class="page-info">Page <?= $page ?> of <?= $total_pages ?></div>
      <div class="nav-buttons">
        <button onclick="window.location.href='?page=1&limit=<?= $rows_per_page ?>&status=<?= $status_filter ?>&sort=<?= $sort_order ?>&search=<?= urlencode($search) ?>'">«</button>
        <button onclick="window.location.href='?page=<?= max(1, $page - 1) ?>&limit=<?= $rows_per_page ?>&status=<?= $status_filter ?>&sort=<?= $sort_order ?>&search=<?= urlencode($search) ?>'">‹</button>
        <button onclick="window.location.href='?page=<?= min($total_pages, $page + 1) ?>&limit=<?= $rows_per_page ?>&status=<?= $status_filter ?>&sort=<?= $sort_order ?>&search=<?= urlencode($search) ?>'">›</button>
        <button onclick="window.location.href='?page=<?= $total_pages ?>&limit=<?= $rows_per_page ?>&status=<?= $status_filter ?>&sort=<?= $sort_order ?>&search=<?= urlencode($search) ?>'">»</button>
      </div>
    </div>
  </div>
</div>

<script>
function changeLimit(limit) {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set('limit', limit);
  urlParams.set('page', 1);
  window.location.search = urlParams.toString();
}
</script>

</body>
</html>
