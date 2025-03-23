<?php
session_start();
include '../includes/db-connection.php';

$sit_in_rows = '';

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);

$sql = "SELECT id, 
               id_no, 
               name, 
               purpose, 
               lab_number, 
               remaining_sessions  
        FROM sit_in_records 
        ORDER BY id ASC"; 

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sit_in_id = htmlspecialchars($row['id']);
        $id_no = htmlspecialchars($row['id_no']);
        $name = htmlspecialchars($row['name']);
        $purpose = htmlspecialchars($row['purpose']);
        $lab_number = htmlspecialchars($row['lab_number']);
        $remaining_sessions = htmlspecialchars($row['remaining_sessions']);

        // Determine status based on remaining sessions
        $status = ($remaining_sessions > 0) ? "Active" : "Inactive";

        $sit_in_rows .= "
        <tr id='row_$sit_in_id'>
            <td class='sit-in-id'>$sit_in_id</td>
            <td>$id_no</td>
            <td>$name</td>
            <td>$purpose</td>
            <td>$lab_number</td>
            <td>$remaining_sessions</td>
            <td class='status'>$status</td>
            <td>
                <button class='logout-btn' data-id='$sit_in_id'>Log-out</button>
            </td>
        </tr>";
    }
} else {
    $sit_in_rows = "<tr><td colspan='8'>No active sit-ins found.</td></tr>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Current Sit-In</title>
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
        table td {
            text-align: center;
        }

        table th {
            background-color: #0d121e;
            cursor: pointer;
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

        .logout-btn {
            background-color: white;
            color: #333;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 20px;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }

        .logout-btn:hover {
            background-color: whitesmoke;
        }

        td:last-child {
            position: relative;
            text-align: center;
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

        .sortable {
            display: flex;
            align-items: center;
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
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
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
            color: white;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Current Sit-In</h1>
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search">
            </div>
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
                    <?php echo $sit_in_rows; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="successNotification" class="notification">
        <i class="fas fa-check-circle"></i> 
        <span><?php echo $success_message; ?></span>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let ascending = true; 

        document.getElementById("sortSitInNumber").addEventListener("click", function() {
            let table = document.getElementById("sitInTable");
            let rows = Array.from(table.rows);
            let sortIcon = document.getElementById("sortIcon");

            rows.sort((a, b) => {
                let valA = parseInt(a.querySelector(".sit-in-id").textContent);
                let valB = parseInt(b.querySelector(".sit-in-id").textContent);
                return ascending ? valA - valB : valB - valA;
            });

        ascending = !ascending; 
        sortIcon.textContent = ascending ? "▲" : "▼";
        sortIcon.classList.toggle("asc", ascending);

        table.innerHTML = "";
        rows.forEach(row => table.appendChild(row));
    });
});

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("sitInTable").addEventListener("click", function (event) {
            if (event.target.classList.contains("logout-btn")) {
                const sitInId = event.target.getAttribute("data-id");

                if (confirm("Do you want to log out this user?")) {
                    fetch(`../includes/logout-student.php?id=${sitInId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                document.getElementById(`row_${sitInId}`).remove(); 
                                showNotification(data.message); 
                            } else {
                                alert("Failed to log out: " + data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                }
            }
        });
    });


        function showNotification(message) {
            const notification = document.getElementById("successNotification");
            notification.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
            notification.classList.add("show");

            setTimeout(() => {
                notification.classList.remove("show");
            }, 3000);
}

        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toUpperCase();
            let rows = document.querySelectorAll("#sitInTable tr");
            rows.forEach(row => {
                let text = row.textContent || row.innerText;
                row.style.display = text.toUpperCase().includes(filter) ? "" : "none";
            });
        });

    </script>
</body>
</html>
