<?php
include '../includes/db-connection.php';

$student_rows = '';
$sql = "SELECT id_no AS student_id, firstname, middlename, lastname, yr_level, UPPER(course) AS course, remaining_sessions
        FROM users WHERE role != 'admin' OR role IS NULL ORDER BY firstname ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = htmlspecialchars($row['student_id'], ENT_QUOTES, 'UTF-8');
        $firstname = ucfirst(strtolower($row['firstname']));
        $middlename = !empty($row['middlename']) ? strtoupper(substr($row['middlename'], 0, 1)) . '.' : '';
        $lastname = ucfirst(strtolower($row['lastname']));
        $student_name = htmlspecialchars(trim("$firstname $middlename $lastname"), ENT_QUOTES, 'UTF-8');
        $yr_level = htmlspecialchars($row['yr_level'], ENT_QUOTES, 'UTF-8');
        $course = htmlspecialchars($row['course'], ENT_QUOTES, 'UTF-8');
        $remaining_sessions = htmlspecialchars($row['remaining_sessions'], ENT_QUOTES, 'UTF-8');

        $student_id_js = json_encode($student_id);
        $student_name_js = json_encode($student_name);
        $yr_level_js = json_encode($yr_level);
        $course_js = json_encode($course);
        $remaining_sessions_js = json_encode($remaining_sessions);

        $student_rows .= "<tr>
    <td>$student_id</td>
    <td>$student_name</td>
    <td>$yr_level</td>
    <td>$course</td>
    <td>$remaining_sessions</td>
    <td class='action-cell'>
        <button class='edit-btn' onclick='openEditModal($student_id_js, $student_name_js, $yr_level_js, $course_js, $remaining_sessions_js)' title='Edit'>
            <i class='fas fa-edit'></i>
        </button>
        <button class='delete-btn' onclick='confirmDelete($student_id_js)' title='Delete'>
            <i class='fas fa-trash-alt'></i>
        </button>
        <button class='reset-btn' onclick='resetStudent($student_id_js)' title='Reset Sessions'>
            <i class='fas fa-undo'></i>
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
  <title>Admin - Students List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; display: flex; background-color: #0d121e; color: white; }
    .main-content { margin-left: 80px; padding: 20px; flex: 1; }
    .sidebar:hover ~ .main-content { margin-left: 180px; }
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 2px solid #333; }
    .table-container { margin-top: 20px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    table th, table td { padding: 15px; text-align: center; }
    table th { background-color: #0d121e; }
    table tr:nth-child(even) { background-color: #111524; }
    table tr:nth-child(odd) { background-color: #212b40; }
    table tr:hover { background-color: #181a25; }
    .action-cell button { background: none; border: none; cursor: pointer; font-size: 18px; margin: 0 5px; color: white; }
    .action-cell button:hover { opacity: 0.7; }
    .add-button, .reset-all-btn { width: 40px; height: 40px; border-radius: 50%; background-color: #212b40; color: #fff; border: none; cursor: pointer; margin-left: 10px; }
    .add-button:hover, .reset-all-btn:hover { opacity: 0.7; }
    .modal { display: none; position: fixed; z-index: 10; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: flex-start; }
    .modal-content { background-color: #0d121e; margin: 50px auto; padding: 20px; border-radius: 8px; width: 50%; max-width: 500px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; }
    .modal-body form { display: flex; flex-direction: column; gap: 15px; }
    .modal-body label { font-weight: bold; display: block; margin-bottom: 10px; }
    .modal-body input, .modal-body select { font-size: 1em; width: 100%; padding: 5px; border: 1px solid #333; border-radius: 5px; background-color: #212b40; color: white; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 2px solid #ddd; margin-top: 20px; padding-top: 10px; }
    .modal-footer button { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
    .modal-footer button:first-child { background-color: #6c757d; color: white; }
    .modal-footer button:last-child { background-color: #007bff; color: white; }
    .close {cursor: pointer;font-size: 1.5em;color: #777;transition: color 0.3s; }
    .close:hover {color: red;}
    .pagination-wrapper { display: flex; justify-content: flex-end; align-items: center; gap: 15px; padding: 15px 20px 0 0; color: white; font-size: 14px; }
    .pagination-wrapper select { background-color: #212b40; color: white; border: 1px solid #555; border-radius: 4px; padding: 5px 8px; }
    .nav-buttons button { background-color: #212b40; color: white; border: none; padding: 6px 10px; margin-left: 5px; cursor: pointer; font-size: 16px; border-radius: 4px; }
    .nav-buttons button:hover { background-color: #2e3b5e; }
  </style>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
<header>
  <h1>Admin - Student's List</h1>
  <div class="search-container">
    <button class="reset-all-btn" onclick="resetAllSessions()" title="Reset All Sessions">
      <i class="fas fa-sync-alt"></i>
    </button>
  </div>
</header>

<?php if (isset($_GET['reset_success'])): ?>
<script>alert('Remaining sessions successfully reset!');</script>
<?php endif; ?>

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
      <?= $student_rows ?>
    </tbody>
  </table>
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
          <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
        </select>
        <label for="editCourse">Course:</label>
        <select id="editCourse" name="course">
          <option value="BSIT">BSIT</option><option value="BSCS">BSCS</option><option value="BSCpE">BSCpE</option><option value="BSED">BSED</option><option value="BSHM">BSHM</option>
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

function resetStudent(studentId) {
  if (confirm(`Reset remaining sessions for Student ID: ${studentId}?`)) {
    window.location.href = `../includes/reset-sessions.php?id=${studentId}`;
  }
}
function resetAllSessions() {
    if (confirm("Are you sure you want to reset all students' sessions?")) {
      window.location.href = '../includes/reset-all-sessions.php';
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
let currentPage = 1;
let rowsPerPage = 10;
const table = document.getElementById("studentTable");
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
function goToLastPage() { currentPage = Math.ceil(allRows.length / rowsPerPage); displayPage(); }

window.onload = displayPage;
</script>

</body>
</html>
