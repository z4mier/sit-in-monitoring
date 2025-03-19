<!--CURRENT SIT-IN -->

<?php
// Include database connection
include '../includes/db-connection.php';

// Initialize $student_rows
$student_rows = '';

// Fetch sit-in records from `sit_in_records` table
$sql = "SELECT id_no AS student_id, 
               name AS student_name, 
               purpose, 
               lab_number, 
               remaining_sessions, 
               timestamp
        FROM sit_in_records
        ORDER BY timestamp DESC";

$result = $conn->query($sql);

// Check query result and build rows
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_rows .= "<tr>
            <td>" . htmlspecialchars($row['student_id']) . "</td>
            <td>" . htmlspecialchars(isset($row['student_name']) ? $row['student_name'] : "Unknown") . "</td>
            <td>" . htmlspecialchars($row['purpose']) . "</td>
            <td>" . htmlspecialchars($row['lab_number']) . "</td>
            <td>" . htmlspecialchars($row['remaining_sessions']) . "</td>
        </tr>";
    }
} else {
    $student_rows = "<tr><td colspan='6'>No sit-in records found.</td></tr>";
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Students Sit-In Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            transition: margin-left 0.3s;
            flex: 1;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 250px;
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
            border-radius: 10px;
            padding: 20px;   
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
        }

        table th {
            background-color: #0d121e;
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
            border-radius: 5px;
            width: 200px;
            background-color: #333;
            color: #fff;
        }

        .search-container .search-icon {
            position: absolute;
            left: 10px;
            color: #fff;
        }

        .add-button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../includes/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Students Sit-In Records</h1>
            <div class="search-container">  
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search">
                <button class="add-button" id="openModalBtn"><i class="fas fa-plus"></i></button>
            </div>
        </header>

        <!-- Table Section -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID NUMBER</th>
                        <th>NAME</th>
                        <th>PURPOSE</th>
                        <th>LAB #</th>
                        <th>REMAINING SESSIONS</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <?php echo $student_rows; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include Sit-In Form Modal -->
    <?php include '../includes/sit-in-form.php'; ?>

    <script>
        // Search Functionality
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toUpperCase();
            let rows = document.querySelectorAll("#studentTable tr");

            rows.forEach(row => {
                let text = row.textContent || row.innerText;
                row.style.display = text.toUpperCase().includes(filter) ? "" : "none";
            });
        });

        // Open Sit-In Modal
        document.getElementById("openModalBtn").addEventListener("click", function() {
            document.getElementById("sitInModal").style.display = "block";
        });

        document.addEventListener("DOMContentLoaded", function () {
        console.log("DOM fully loaded!");

        let sitInForm = document.getElementById("sitInForm");
        
        if (sitInForm) {
            sitInForm.addEventListener("submit", function () {
                console.log("Form is being submitted!");
            });
        } else {
            console.log("sitInForm not found!");
        }
    });
    </script>
</body>
</html>
