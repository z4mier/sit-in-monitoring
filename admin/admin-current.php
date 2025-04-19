<?php
session_start();
include '../includes/db-connection.php';

$sit_in_rows = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_no'])) {
    $id_no = $_POST['id_no'];
    $name = $_POST['name'];
    $purpose = $_POST['purpose'];
    $lab_number = $_POST['lab_number'];
    $remaining_sessions = (int)$_POST['remaining_sessions'];

    $check_stmt = $conn->prepare("SELECT id FROM sit_in_records WHERE id_no = ? AND status = 'Active'");
    $check_stmt->bind_param("s", $id_no);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['notification_message'] = "Sit-In failed: Student already has an active record.";
        $_SESSION['notification_type'] = "error";
        header("Location: admin-current.php");
        exit();
    }

    if ($remaining_sessions <= 0) {
        $_SESSION['notification_message'] = "Sit-In failed: No remaining sessions.";
        $_SESSION['notification_type'] = "error";
        header("Location: admin-current.php");
        exit();
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO sit_in_records (id_no, name, purpose, lab_number, remaining_sessions, status, time_in) VALUES (?, ?, ?, ?, ?, 'Active', NOW())");
        $stmt->bind_param("ssssi", $id_no, $name, $purpose, $lab_number, $remaining_sessions);
        $stmt->execute();

        $new_remaining = $remaining_sessions - 1;
        $stmt2 = $conn->prepare("UPDATE users SET remaining_sessions = ? WHERE id_no = ?");
        $stmt2->bind_param("is", $new_remaining, $id_no);
        $stmt2->execute();

        $conn->commit();
        $_SESSION['notification_message'] = "Sit-In successfully recorded for $name!";
        $_SESSION['notification_type'] = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['notification_message'] = "Sit-In failed: " . $e->getMessage();
        $_SESSION['notification_type'] = "error";
    }

    header("Location: admin-current.php");
    exit();
}

if (isset($_POST['time_out'])) {
    $sit_in_id = $_POST['sit_in_id'];
    $time_out = date("Y-m-d H:i:s");

    $sql = "UPDATE sit_in_records SET status = 'Offline', time_out = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $time_out, $sit_in_id);

    if ($stmt->execute()) {
        $_SESSION['notification_message'] = "Student successfully timed out!";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification_message'] = "Error while timing out the student.";
        $_SESSION['notification_type'] = "error";
    }

    header("Location: admin-current.php");
    exit();
}

$notification_message = $_SESSION['notification_message'] ?? '';
$notification_type = $_SESSION['notification_type'] ?? '';
unset($_SESSION['notification_message'], $_SESSION['notification_type']);

$sql = "SELECT r.id, r.id_no, r.name, r.purpose, r.lab_number, r.time_in, r.time_out, r.status, u.remaining_sessions
        FROM sit_in_records r
        JOIN users u ON r.id_no = u.id_no
        WHERE r.status = 'Active'
        ORDER BY r.id DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sit_in_id = htmlspecialchars($row['id']);
        $id_no = htmlspecialchars($row['id_no']);
        $name = htmlspecialchars($row['name']);
        $purpose = htmlspecialchars($row['purpose']);
        $lab_number = htmlspecialchars($row['lab_number']);
        $remaining_sessions = htmlspecialchars($row['remaining_sessions']);
        $time_in = htmlspecialchars($row['time_in']);
        $time_out = htmlspecialchars($row['time_out'] ?? '—');
        $status = htmlspecialchars($row['status']);

        if ($time_out !== '—') {
            $time_out = date('Y-m-d H:i:s', strtotime($time_out));
        }

        $sit_in_rows .= "
        <tr id='row_$sit_in_id'>
            <td class='sit-in-id'>$sit_in_id</td>
            <td>$id_no</td>
            <td>$name</td>
            <td>$purpose</td>
            <td>$lab_number</td>
            <td>$remaining_sessions</td>
            <td>$status</td>
            <td>
                <form method='POST' action='admin-current.php'>
                    <input type='hidden' name='sit_in_id' value='$sit_in_id'>
                    <button type='submit' name='time_out' class='logout-btn'>Time Out</button>
                </form>
            </td>
        </tr>";
    }
} else {
    $sit_in_rows = "<tr><td colspan='10'>No active sit-in records found.</td></tr>";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Current Sit-In</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      display: flex;
      background-color: #0d121e;
      color: #ffffff;
    }
    .main-content {
      margin-left: 80px;
      padding: 20px;
      flex: 1;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 2px solid #333;
    }
    .sidebar:hover ~ .main-content {
            margin-left: 180px;
        }
    .table-container {
      margin-top: 20px;
      border-radius: 10px;
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th, table td {
      padding: 15px;
      text-align: center;

    }
    thead tr {
  background-color: transparent !important;
}

    table td {
            text-align: center;
        }
    table tr:nth-child(even) {
      background-color: #111524;
    }
    table tr:nth-child(odd) {
      background-color: #212b40;
    }
    table tr:hover {
      background-color: #181a25;
    }
    td:last-child {
            position: relative;
            text-align: center;
        }

    .logout-btn {
      background-color: white;
      color: #333;
      border: none;
      padding: 10px;
      cursor: pointer;
      border-radius: 20px;
      font-weight: bold;
    }
    .logout-btn:hover {
      background-color: whitesmoke;
    }
    .notification {
      display: none;
      position: fixed;
      top: 20px; 
      left: 50%;
      transform: translateX(-50%); 
      background-color: #181a25; 
      color: white;
      padding: 15px 25px;
      border-radius: 20px;
      font-size: 15px;
      z-index: 1000;
      text-align: center;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .notification.show {
      display: flex;
      opacity: 1;
    }
    .notification i {
      font-size: 15px;
    }
    .notification.success i {
      color: #4ade80;
    }
    .notification.error i {
      color: #f87171;
    }
    .sort-arrow {
      margin-left: 5px;
      transition: transform 0.3s;
    }
    .sort-arrow.asc {
      transform: rotate(180deg);
    }
    td:nth-child(1) {
      text-align: left;
      padding-left: 70px;
    }
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

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
  <header>
    <h1>Current Sit-In</h1>
  </header>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th class="sortable" id="sortSitInNumber" style="text-align: left;">
            Sit-In Number <span class="sort-arrow" id="sortIcon">▲</span>
          </th>
          <th>ID Number</th>
          <th>Name</th>
          <th>Purpose</th>
          <th>Lab Number</th>
          <th>Session</th>
          <th>Status</th>
          <th>Actions</th>
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


<!-- ✅ Dynamic Notification -->
<div id="successNotification" class="notification <?= $notification_type ?>">
  <i class="<?= $notification_type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle' ?>"></i>
  <span id="notificationMessage"><?= $notification_message ?></span>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Sort by Sit-In #
    let ascending = true;
    document.getElementById("sortSitInNumber").addEventListener("click", function () {
        const table = document.getElementById("sitInTable");
        const rows = Array.from(table.rows);
        const sortIcon = document.getElementById("sortIcon");

        rows.sort((a, b) => {
            const valA = parseInt(a.querySelector(".sit-in-id").textContent);
            const valB = parseInt(b.querySelector(".sit-in-id").textContent);
            return ascending ? valA - valB : valB - valA;
        });

        ascending = !ascending;
        sortIcon.textContent = ascending ? "▲" : "▼";
        sortIcon.classList.toggle("asc", ascending);

        table.innerHTML = "";
        rows.forEach(row => table.appendChild(row));
    });

    // Show Notification if Message Exists
    const message = <?= json_encode($notification_message) ?>;
    const type = <?= json_encode($notification_type) ?>;
    if (message) {
        showNotification(message, type);
    }
});

function showNotification(message, type) {
    const box = document.getElementById("successNotification");
    const icon = box.querySelector("i");
    const text = document.getElementById("notificationMessage");

    box.className = `notification ${type} show`;
    icon.className = type === "success" ? "fas fa-check-circle" : "fas fa-times-circle";
    text.textContent = message;

    setTimeout(() => {
        box.classList.remove("show");
    }, 3000);
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
