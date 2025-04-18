<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Sidebar</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #0d121e;
      color: white;
    }

    .sidebar {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      width: 80px;
      height: 100vh;
      background-color: #0d121e;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px 0;
      border-right: 3px solid #181a25;
      transition: width 0.3s;
      z-index: 999;
    }

    .sidebar:hover {
      width: 180px;
    }

    .sidebar a {
      color: whitesmoke;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      transition: background-color 0.3s;
      border-radius: 10px;
      font-size: 15px;
    }

    .sidebar a:hover {
      background-color: #181a25;
    }

    .sidebar i {
      font-size: 17px;
      margin-right: 12px;
      margin-left: 5px;
    }

    .sidebar span {
      display: none;
      white-space: nowrap;
    }

    .sidebar:hover span {
      display: inline;
    }

    .search-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .search-box {
      background: #0d121e;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      text-align: center;
      position: relative;
    }

    .search-box input {
      width: 70%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
      border: none;
      background: white;
      color: black;
    }

    .search-box button {
      margin-top: 10px;
      padding: 10px;
      border: none;
      
      border-radius: 5px;
      cursor: pointer;
    }

    .close-search-modal {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
      color: whitesmoke;
      background: none;
      font-weight: bold;
      border: none;
    }

    #sitInFormContainer {
      display: none;
      margin-left: 100px;
      padding: 30px;
    }
  </style>
</head>
<body>

  <!--  SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-links">
      <a href="admin-home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="#" onclick="openModal()"><i class="fas fa-search"></i><span>Search</span></a>
      <a href="admin-students.php"><i class="fas fa-users"></i><span>Students</span></a>
      <a href="admin-announcements.php"><i class="fas fa-bullhorn"></i><span>Announcement</span></a>
      <a href="admin-current.php"><i class="fas fa-calendar-check"></i><span>Current Sit-In</span></a>
      <a href="admin-records.php"><i class="fas fa-folder"></i><span>Records</span></a>
    </div>
    <div class="sidebar-logout" style="margin-top: 450px; padding-left: 5px; margin-bottom: auto;">
  <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
</div>

  </div>

  <!-- SEARCH MODAL -->
  <div class="search-overlay" id="searchModal">
    <div class="search-box">
      <button class="close-search-modal" onclick="closeModal()">&times;</button>
      <h2>Search Student</h2>
      <input type="text" id="searchInput" placeholder="Enter Student ID or Name">
      <button onclick="searchStudent()">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </div>

  <!-- SIT-IN FORM CONTAINER -->
  <div id="sitInFormContainer"></div>

  <script>
    function openModal() {
      document.getElementById('searchModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('searchModal').style.display = 'none';
    }

    function searchStudent() {
      const query = document.getElementById('searchInput').value.trim();

      if (!query) {
        alert("Please enter a Student ID or Name.");
        return;
      }
      fetch("../includes/search-student.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "query=" + encodeURIComponent(query)
    })

      .then(response => {
        if (!response.ok) throw new Error("Failed to fetch student.");
        return response.text();
      })
      .then(html => {
        const container = document.getElementById("sitInFormContainer");
        container.innerHTML = html;
        container.style.display = "block";
        closeModal();
      })
      .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while fetching the sit-in form.");
      });   
    }
  </script>
</body>
</html>
