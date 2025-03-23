<?php
// Include database connection
include '../includes/db-connection.php';

// Initialize $student_rows
$student_rows = '';

// Fetch registered students from `users` table
$sql = "SELECT id_no AS student_id, 
               CONCAT(firstname, ' ', lastname) AS student_name, 
               yr_level, 
               course, 
               remaining_sessions
        FROM users
        WHERE role != 'admin' OR role IS NULL
        ORDER BY student_name ASC";

$result = $conn->query($sql);

// Check query result and build rows
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = htmlspecialchars($row['student_id']);
        $student_name = htmlspecialchars($row['student_name']);
        $yr_level = htmlspecialchars($row['yr_level']);
        $course = htmlspecialchars($row['course']);
        $remaining_sessions = htmlspecialchars($row['remaining_sessions']);

        $student_rows .= "
        <tr>
            <td>$student_id</td>
            <td>$student_name</td>
            <td>$yr_level</td>
            <td>$course</td>
            <td>$remaining_sessions</td>
            <td class='action-cell'>
                <div class='dropdown'>
                    <button class='action-btn'><i class='fas fa-ellipsis-v'></i></button>
                    <div class='dropdown-menu'>
                        <button class='update-btn' data-id='$student_id'>
                            <i class='fas fa-edit'></i> Update User
                        </button>
                        <button class='delete-btn' data-id='$student_id'>
                            <i class='fas fa-trash-alt'></i> Delete User
                        </button>
                    </div>
                </div>
            </td>
        </tr>";
    }
} else {
    $student_rows = "<tr><td colspan='6'>No registered students found.</td></tr>";
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
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #212b40;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        /* Dropdown Menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: white;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: #0d121e;
            border-radius: 5px;
            padding: 10px;
            min-width: 120px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            z-index: 10;
        }

        .dropdown-menu button {
            width: 100%;
            padding: 8px;
            background: none;
            border: none;
            color: white;
            text-align: left;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .dropdown-menu button i {
            font-size: 16px;
        }

        .dropdown-menu button:hover {
            background-color: #181a25;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../includes/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Student's Information</h1>
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
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Year Level</th>
                        <th>Course</th>
                        <th>Remaining Sessions</th>
                        <th>Actions</th>
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

        // Show dropdown menu when clicking the 3-dot button
        document.querySelectorAll(".action-btn").forEach(button => {
            button.addEventListener("click", function (event) {
                event.stopPropagation();
                let dropdown = this.nextElementSibling;
                
                document.querySelectorAll(".dropdown-menu").forEach(menu => {
                    if (menu !== dropdown) menu.style.display = "none";
                });

                dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
            });
        });

        // Add event listeners for Update and Delete actions
        document.querySelectorAll(".update-btn").forEach(button => {
            button.addEventListener("click", function() {
                const studentId = this.getAttribute("data-id");
                window.location.href = `../includes/update-student.php?id=${studentId}`;
            });
        });

        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function() {
                const studentId = this.getAttribute("data-id");
                if (confirm(`Are you sure you want to delete Student ID: ${studentId}?`)) {
                    fetch(`../includes/delete-student.php?id=${studentId}`, {
                        method: "POST"
                    })
                    .then(response => {
                        if (response.ok) {
                            alert(`Student ID: ${studentId} has been deleted.`);
                            window.location.reload();
                        } else {
                            alert("Failed to delete the student. Please try again.");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred while deleting the student.");
                    });
                }
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener("click", function () {
            document.querySelectorAll(".dropdown-menu").forEach(menu => {
                menu.style.display = "none";
            });
        });

        // Prevent dropdown from closing when clicking inside
        document.querySelectorAll(".dropdown-menu").forEach(menu => {
            menu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });
    </script>
</body>
</html>
