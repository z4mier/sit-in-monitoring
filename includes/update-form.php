<?php
// Include database connection
include '../includes/db-connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link your CSS file -->
    
    <style>
        /* Modal Background Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            align-items: flex-start;
        }

        /* Modal Content */
        .modal-content {
            background-color: #0d121e;
            margin-top: 50px;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s;
        }

        /* Header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
        }

        .modal-header h2 {
            color: white;
        }

        .close {
            cursor: pointer;
            font-size: 1.5em;
            color: #777;
            transition: color 0.3s;
        }

        .close:hover {
            color: #ff5c5c;
        }

        /* Form Styling */
        .modal-body form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-body label {
            font-weight: bold;
            color: white;
            display: block;
            margin-bottom: 10px; 
        }

        .modal-body input, .modal-body select {
            width: 100%;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 5px;
            font-size: 1em;
            background-color: #212b40;
            color: white;
            outline: none;
        }

        .modal-body select option {
            background-color: #212b40;
            color: white;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 10px;
        }

        .btn-close {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-close:hover {
            background-color: #d32f2f;
        }

        .btn-update {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-update:hover {
            background-color: rgb(21, 88, 245);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10%); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- Student List -->
<table border="1">
    <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Year Level</th>
            <th>Course</th>
            <th>Remaining Sessions</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch students
        $sql = "SELECT id_no, firstname, lastname, yr_level, course, remaining_sessions FROM users WHERE role != 'admin' OR role IS NULL ORDER BY firstname ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id_no'];
                $name = $row['firstname'] . " " . $row['lastname'];
                $year = $row['yr_level'];
                $course = $row['course'];
                $sessions = $row['remaining_sessions'];

                echo "<tr>
                    <td>$id</td>
                    <td>$name</td>
                    <td>$year</td>
                    <td>$course</td>
                    <td>$sessions</td>
                    <td><button class='btn-update-student' data-id='$id' data-name='$name' data-year='$year' data-course='$course' data-sessions='$sessions'>Update</button></td>
                </tr>";
            }
        }
        ?>
    </tbody>
</table>

<!-- Update Student Modal -->
<div id="updateStudentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Student</h2>
            <span class="close" id="closeModalBtn">&times;</span>
        </div>
        <div class="modal-body">
            <form id="updateStudentForm" action="../includes/update-student-process.php" method="POST">
                <input type="hidden" id="updateId" name="idNumber">
                
                <div>
                    <label for="updateName">Student Name:</label>
                    <input type="text" id="updateName" name="studentName" readonly>
                </div>

                <div>
                    <label for="updateYear">Year Level:</label>
                    <select id="updateYear" name="yr_level" required>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>

                <div>
                    <label for="updateCourse">Course:</label>
                    <input type="text" id="updateCourse" name="course" required>
                </div>

                <div>
                    <label for="updateSessions">Remaining Sessions:</label>
                    <input type="number" id="updateSessions" name="remaining_sessions" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-close" id="closeModalBtnFooter">Cancel</button>
                    <button type="submit" class="btn-update">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("updateStudentModal");
    var closeModalBtns = document.querySelectorAll("#closeModalBtn, #closeModalBtnFooter");
    var updateButtons = document.querySelectorAll(".btn-update-student");

    updateButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("updateId").value = this.dataset.id;
            document.getElementById("updateName").value = this.dataset.name;
            document.getElementById("updateYear").value = this.dataset.year;
            document.getElementById("updateCourse").value = this.dataset.course;
            document.getElementById("updateSessions").value = this.dataset.sessions;
            
            modal.style.display = "block";
        });
    });

    closeModalBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
</script>

</body>
</html>
