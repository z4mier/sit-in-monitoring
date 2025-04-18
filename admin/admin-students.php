<?php
include '../includes/db-connection.php';

$student_rows = '';
$sql = "SELECT id_no AS student_id, CONCAT(firstname, ' ', lastname) AS student_name, yr_level, course, remaining_sessions
        FROM users WHERE role != 'admin' OR role IS NULL ORDER BY student_name ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = htmlspecialchars($row['student_id'], ENT_QUOTES, 'UTF-8');
        $student_name = htmlspecialchars($row['student_name'], ENT_QUOTES, 'UTF-8');
        $yr_level = htmlspecialchars($row['yr_level'], ENT_QUOTES, 'UTF-8');
        $course = htmlspecialchars($row['course'], ENT_QUOTES, 'UTF-8');
        $remaining_sessions = htmlspecialchars($row['remaining_sessions'], ENT_QUOTES, 'UTF-8');

        $student_id_js = json_encode($student_id);
        $student_name_js = json_encode($student_name);
        $yr_level_js = json_encode($yr_level);
        $course_js = json_encode($course);
        $remaining_sessions_js = json_encode($remaining_sessions);

        $student_rows .= "
<tr>
    <td>$student_id</td>
    <td>$student_name</td>
    <td>$yr_level</td>
    <td>$course</td>
    <td>$remaining_sessions</td>
    <td class='action-cell'>
        <button class='edit-btn' onclick='openEditModal($student_id_js, $student_name_js, $yr_level_js, $course_js, $remaining_sessions_js)'>
            <i class='fas fa-edit' style='color: white;'></i>
        </button>
        <button class='delete-btn' onclick='confirmDelete($student_id_js)'>
            <i class='fas fa-trash-alt' style='color: white;'></i>
        </button>
    </td>
</tr>";
    }
} else {
    $student_rows = "<tr><td colspan='6'>No registered students found.</td></tr>";
}

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
            color: white;
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
            color: white;
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
        .add-button:hover{
            opacity: 0.7;
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

        .action-cell {
            text-align: center;
        }

        .action-cell button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            margin: 0 5px;
            color: white;
        }

        .action-cell button:hover {
            opacity: 0.7;
        }
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
            align-items: flex-start; 
        }
        .modal-content {
            background-color: #0d121e;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5em;
            color: white;
        }

        .close {
            cursor: pointer;
            font-size: 1.5em;
            color: #777;
            transition: color 0.3s;
        }

        .close:hover {
            color: red;
        }

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
        .modal-body label:first-of-type {
             margin-top: 15px;
}
        .modal-body input,
        .modal-body select {
            width: 100%;
            box-sizing: border-box;
            padding: 5px;
            border: 1px solid #333;
            border-radius: 5px;
            font-size: 1em;
            background-color: #212b40;
            color: white;
            transition: border 0.3s;
            outline: none;
        }

        .modal-body input:focus,
        .modal-body select:focus {
            border: 1px solid #007bff;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 2px solid #ddd;
            margin-top: 20px;
            padding-top: 10px;
        }

        .modal-footer button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        .modal-footer button:first-child {
            background-color: #6c757d;
            color: white;
        }

        .modal-footer button:first-child:hover {
            background-color: #5a6268;
        }

        .modal-footer button:last-child {
            background-color: #007bff;
            color: white;
        }

        .modal-footer button:last-child:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
 
    <!-- Sidebar -->
    <?php include '../includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Student's Information</h1>
            <div class="search-container">  
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search">
                <button class="add-button" id="openModalBtn"><i class="fas fa-plus"></i></button>
            </div>
        </header>

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
        <!-- Edit Student Modal -->
<div id="editStudentModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Student</h2>
      <span class="close" onclick="closeEditModal()">&times;</span>
    </div>

    <div class="modal-body">
      <form id="editStudentForm">
        <input type="hidden" name="old_id" id="oldStudentId">

        <label for="editStudentId">ID Number:</label>
        <input type="text" name="id" id="editStudentId" required>

        <label for="editStudentName">Name:</label>
        <input type="text" name="name" id="editStudentName" required>

        <label for="editYrLevel">Year Level:</label>
        <select id="editYrLevel" name="yr_level">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
        </select>

        <label for="editCourse">Course:</label>
        <select id="editCourse" name="course">
          <option value="BSIT">BSIT</option>
          <option value="BSCS">BSCS</option>
          <option value="BSCpE">BSCpE</option>
          <option value="BSED">BSED</option>
          <option value="BSHM">BSHM</option>
        </select>

        <label for="editSessions">Remaining Sessions:</label>
        <input type="number" name="remaining_sessions" id="editSessions" value="30" readonly>

        <div class="modal-footer">
          <button type="button" onclick="closeEditModal()">Cancel</button>
          <button type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("editStudentModal");

    if (modal) modal.style.display = "none";

    // 🔎 Search Filtering
    document.getElementById("searchInput").addEventListener("keyup", function () {
      const filter = this.value.toUpperCase();
      const rows = document.querySelectorAll("#studentTable tr");

      rows.forEach(row => {
        const text = row.textContent || row.innerText;
        row.style.display = text.toUpperCase().includes(filter) ? "" : "none";
      });
    });
  });

  function openEditModal(id, name, yrLevel, course, remainingSessions) {
    document.getElementById("oldStudentId").value = id;
    document.getElementById("editStudentId").value = id;
    document.getElementById("editStudentName").value = name;
    document.getElementById("editYrLevel").value = yrLevel;
    document.getElementById("editCourse").value = course;
    document.getElementById("editSessions").value = remainingSessions;

    document.getElementById("editStudentModal").style.display = "flex";
  }

  function closeEditModal() {
    document.getElementById("editStudentModal").style.display = "none";
  }

  function confirmDelete(studentId) {
    if (confirm(`Are you sure you want to delete Student ID: ${studentId}?`)) {
      window.location.href = `../includes/delete-student.php?id=${studentId}`;
    }
  }

  document.getElementById("editStudentForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("../includes/update-student.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      if (data.includes("Error")) {
        alert("Failed to update: " + data);
      } else {
        alert("Update successful!");
        closeEditModal();

        const oldStudentId = document.getElementById("oldStudentId").value;
        const studentId = document.getElementById("editStudentId").value;
        const studentName = document.getElementById("editStudentName").value;
        const yrLevel = document.getElementById("editYrLevel").value;
        const course = document.getElementById("editCourse").value;
        const remainingSessions = document.getElementById("editSessions").value;

        const tableRows = document.querySelectorAll("#studentTable tr");
        tableRows.forEach(row => {
          if (row.cells[0].innerText === oldStudentId) {
            row.cells[0].innerText = studentId;
            row.cells[1].innerText = studentName;
            row.cells[2].innerText = yrLevel;
            row.cells[3].innerText = course;
            row.cells[4].innerText = remainingSessions;
          }
        });
      }
    })
    .catch(error => console.error("Error:", error));
  });
</script>

</body>
</html>