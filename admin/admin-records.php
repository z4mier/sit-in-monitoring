<?php
session_start();
include '../includes/db-connection.php';

// Fetch all sit-in records
$sit_in_rows = '';
$notification_message = $_SESSION['notification_message'] ?? '';
$notification_type = $_SESSION['notification_type'] ?? '';
unset($_SESSION['notification_message'], $_SESSION['notification_type']);

// Sorting Parameters
$sort_column = $_GET['sort'] ?? 'date';
$sort_order = $_GET['order'] ?? 'DESC';

// Validate sorting parameters to prevent SQL injection
$allowed_columns = ['id', 'id_no', 'name', 'purpose', 'lab_number', 'time_in', 'time_out', 'date'];
$allowed_orders = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'date'; // Default if invalid
}

if (!in_array($sort_order, $allowed_orders)) {
    $sort_order = 'DESC'; // Default if invalid
}

// Date filtering
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$date_filter = '';

if ($start_date && $end_date) {
    $date_filter = " WHERE date BETWEEN '$start_date' AND '$end_date'";
} elseif ($start_date) {
    $date_filter = " WHERE date >= '$start_date'";
} elseif ($end_date) {
    $date_filter = " WHERE date <= '$end_date'";
}

// Query to fetch sit-in records from the database
$sql = "SELECT r.id, r.id_no, r.name, r.purpose, r.lab_number, r.time_in, r.time_out, r.date
        FROM sit_in_records r
        $date_filter
        ORDER BY $sort_column $sort_order";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sit_in_id = htmlspecialchars($row['id']);
        $id_no = htmlspecialchars($row['id_no']);
        $name = htmlspecialchars($row['name']);
        $purpose = htmlspecialchars($row['purpose']);
        $lab_number = htmlspecialchars($row['lab_number']);
        $time_in = htmlspecialchars($row['time_in']);
        $time_out = htmlspecialchars($row['time_out'] ?? '—'); // Display "—" if time_out is null
        $date = htmlspecialchars($row['date']);

        // Format time_out if it is available
        if ($time_in) {
            $time_in = date('H:i:s', strtotime($time_in)); // Format time_in as time only
        }
        if ($time_out !== '—') {
            $time_out = date('H:i:s', strtotime($time_out)); // Format time_out as time only
        }
        // Append each record to the table row
        $sit_in_rows .= "
        <tr>
            <td>$id_no</td>
            <td>$name</td>
            <td>$purpose</td>
            <td>$lab_number</td>
            <td>$time_in</td>
            <td>$time_out</td>
            <td>$date</td>
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
    <title>Admin - Sit-In Records</title>
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
        .sidebar:hover ~ .main-content {
            margin-left: 180px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #333;
        }
        .table-container {
            margin-top: 20px;
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
        table tr:nth-child(even) {
            background-color: #111524;
        }
        table tr:nth-child(odd) {
            background-color: #212b40;
        }
        table tr:hover {
            background-color: #181a25;
        }
        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }
        .search-container input {
            padding: 10px 10px 10px 30px;
            border: none;
            border-radius: 20px;
            width: 200px;
            background-color: white;
            color: black;
        }
        .search-container .search-icon {
            position: absolute;
            left: 10px;
            color: black;
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
    </style>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
    <header>
        <h1>Sit-In Records</h1>
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search">
        </div>
    </header>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="sortable" data-column="id">
                        ID Number <span class="sort-arrow <?= $sort_column === 'id' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                    <th class="sortable" data-column="name">
                        Name <span class="sort-arrow <?= $sort_column === 'name' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                    <th class="sortable" data-column="purpose">
                        Purpose <span class="sort-arrow <?= $sort_column === 'purpose' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                    <th class="sortable" data-column="lab_number">
                        Lab # <span class="sort-arrow <?= $sort_column === 'lab_number' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                    <th class="sortable" data-column="time_in">
                        Time In <span class="sort-arrow <?= $sort_column === 'time_in' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                    <th class="sortable" data-column="time_out">
                        Time Out <span class="sort-arrow <?= $sort_column === 'time_out' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                    <th class="sortable" data-column="date">
                        Date <span class="sort-arrow <?= $sort_column === 'date' && $sort_order === 'DESC' ? 'desc' : '' ?>"></span>
                    </th>
                </tr>
            </thead>
            <tbody id="sitInTable">
                <?= $sit_in_rows ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let ascending = true;
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-column');
            const currentOrder = '<?= $sort_order ?>';
            const newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
            
            // Update the URL parameters for sorting
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', column);
            urlParams.set('order', newOrder);
            window.location.search = urlParams.toString();
        });
    });

    // Search functionality
    document.getElementById("searchInput").addEventListener("keyup", function () {
        const filter = this.value.toUpperCase();
        document.querySelectorAll("#sitInTable tr").forEach(row => {
            const text = row.textContent || row.innerText;
            row.style.display = text.toUpperCase().includes(filter) ? "" : "none";
        });
    });
});
</script>

</body>
</html>
