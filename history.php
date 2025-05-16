<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$firstname = htmlspecialchars($user['firstname']);
$middlename = isset($user['middlename']) ? htmlspecialchars($user['middlename']) : '';
$lastname = htmlspecialchars($user['lastname']);
$fullname = trim("$firstname $middlename $lastname");
$username = htmlspecialchars($user['username']);

$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_no FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_row = $result->fetch_assoc();
    $id_no = $user_row['id_no'];
} else {
    die("Invalid user.");
}

$history_query = "SELECT * FROM sit_in_records WHERE id_no = ? ORDER BY id DESC";
$history_stmt = $conn->prepare($history_query);
$history_stmt->bind_param("s", $id_no);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sit-In History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/04f888fcdb.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0d121e; margin: 0; padding: 0; color: white; }
        .container { margin-left: 270px; padding: 30px; }
        h2 {
          font-size: 28px;
          border-bottom: 2px solid #333;
          padding-bottom: 15px;
          margin-bottom: 25px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 10px; text-align: left; }
        th { background-color: #181a25; font-weight: bold; }
        tr:nth-child(even) { background-color: #1c2233; }
        tr:nth-child(odd) { background-color: #11141f; }
        .btn-icon { background: none; border: none; color: white; font-size: 16px; cursor: pointer; }
        .btn-icon:hover { color: #ccc; }

        .modal { display: none; position: fixed; z-index: 99; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
        .modal-content {
            background-color: #1c2233;
            padding: 20px;
            border-radius: 12px;
            width: 500px;
            color: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
        }
        .modal-content textarea {
            width: 93%;
            height: 120px;
            padding: 12px 15px;
            border-radius: 10px;
            border: none;
            resize: none;
            font-family: 'Inter', sans-serif;
            background-color: #0d121e;
            color: white;
            font-size: 14px;
            margin-top: 10px;
        }
        .modal-content button {
            margin-top: 15px;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            background-color: white;
            color: #0d121e;
            cursor: pointer;
            font-size: 14px;
        }
        .modal-content button:hover { background-color: #ccc; }
        .modal-content .close { float: right; font-size: 18px; cursor: pointer; color: white; font-weight: bold; }
        .modal-content .close:hover { color: #ccc; }
        .modal-content h3 { margin: 0 0 10px; font-size: 18px; font-weight: 600; }

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

<div class="container">
    <h2>Sit-In History</h2>

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
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="historyTable">
        <?php if ($history_result->num_rows > 0): ?>
            <?php while ($row = $history_result->fetch_assoc()): ?>
                <tr class="history-row">
                    <td><?= htmlspecialchars($row['id_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                    <td><?= htmlspecialchars($row['lab_number']) ?></td>
                    <td><?= date("H:i:s", strtotime($row['time_in'])) ?></td>
                    <td><?= date("H:i:s", strtotime($row['time_out'])) ?></td>
                    <td><?= date("Y-m-d", strtotime($row['time_in'])) ?></td>
                    <td>
                        <button class="btn-icon" onclick="openFeedbackModal(<?= $row['id'] ?>)">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align: center;">No sit-in records found.</td></tr>
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

<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackModal()">&times;</span>
        <h3>Submit Feedback</h3>
        <form method="POST" action="includes/submit-feedback.php">
            <input type="hidden" id="feedback_record_id" name="record_id">
            <textarea name="feedback" placeholder="Enter your feedback here..." required></textarea>
            <div style="text-align: right;">
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openFeedbackModal(recordId) {
        document.getElementById("feedback_record_id").value = recordId;
        document.getElementById("feedbackModal").style.display = "flex";
    }

    function closeFeedbackModal() {
        document.getElementById("feedbackModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("feedbackModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    // Pagination Script
    let currentPage = 1;
    let rowsPerPage = 10;
    const table = document.getElementById("historyTable");
    const allRows = Array.from(table.querySelectorAll(".history-row"));
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
            table.innerHTML = "<tr><td colspan='8' style='text-align:center; padding: 20px; color: #ccc;'>No records to display.</td></tr>";
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
