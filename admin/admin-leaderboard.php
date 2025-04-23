<?php
session_start();
include '../includes/db-connection.php';

$leaderboard_rows = '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows_per_page = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $rows_per_page;

$total_sql = "SELECT COUNT(DISTINCT id_no) AS total FROM sit_in_records";
$total_result = $conn->query($total_sql);
$total_rows = ($total_result && $total_result->num_rows > 0) ? $total_result->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_rows / $rows_per_page);

$sql = "SELECT id_no, name, COUNT(*) AS total_sessions, SUM(points) AS total_points
        FROM sit_in_records
        GROUP BY id_no, name
        ORDER BY total_sessions DESC, total_points DESC
        LIMIT $offset, $rows_per_page";

$result = $conn->query($sql);

$rank = $offset + 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leaderboard_rows .= "
        <tr>
            <td>$rank</td>
            <td>" . htmlspecialchars($row['id_no']) . "</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['total_sessions']) . "</td>
            <td>" . htmlspecialchars($row['total_points']) . "</td>
        </tr>";
        $rank++;
    }
} else {
    $leaderboard_rows = "<tr><td colspan='5'>No participant data available.</td></tr>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Sit-In Leaderboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  
  <style>
      body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; display: flex; }
      .main-content { margin-left: 80px; padding: 20px; flex: 1; }
      .sidebar:hover ~ .main-content { margin-left: 180px; }
      header { padding: 20px; border-bottom: 2px solid #333; }
      h1 { font-size: 28px; margin: 0; }
      .table-container { margin-top: 20px; padding: 20px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 15px; text-align: center; }
      thead tr { background-color: transparent !important; }
      table tr:nth-child(even) { background-color: #111524; }
      table tr:nth-child(odd) { background-color: #212b40; }
      table tr:hover { background-color: #181a25; }
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
    <h1>Top Sit-In Participants</h1>
  </header>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Rank</th>
          <th>ID Number</th>
          <th>Name</th>
          <th>Total Sessions</th>
          <th>Total Points</th>
        </tr>
      </thead>
      <tbody>
        <?= $leaderboard_rows ?>
      </tbody>
    </table>

    <div class="pagination-wrapper">
      <div class="rows-selector">
        Rows per page:
        <select onchange="changeLimit(this.value)">
          <option value="5" <?= $rows_per_page == 5 ? 'selected' : '' ?>>5</option>
          <option value="10" <?= $rows_per_page == 10 ? 'selected' : '' ?>>10</option>
          <option value="20" <?= $rows_per_page == 20 ? 'selected' : '' ?>>20</option>
          <option value="50" <?= $rows_per_page == 50 ? 'selected' : '' ?>>50</option>
        </select>
      </div>
      <div class="page-info">Page <?= $page ?> of <?= $total_pages ?></div>
      <div class="nav-buttons">
        <button onclick="window.location.href='?page=1&limit=<?= $rows_per_page ?>'">«</button>
        <button onclick="window.location.href='?page=<?= max(1, $page - 1) ?>&limit=<?= $rows_per_page ?>'">‹</button>
        <button onclick="window.location.href='?page=<?= min($total_pages, $page + 1) ?>&limit=<?= $rows_per_page ?>'">›</button>
        <button onclick="window.location.href='?page=<?= $total_pages ?>&limit=<?= $rows_per_page ?>'">»</button>
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