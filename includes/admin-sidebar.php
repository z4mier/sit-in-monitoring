<?php
$conn = new mysqli("localhost", "root", "", "sysarch");
$notif_count = 0;

$result = $conn->query("SELECT COUNT(*) AS count FROM reservations WHERE status = 'Pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $notif_count = (int)$row['count'];
}
?>
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
      position: relative;
    }

    .sidebar a:hover {
      background-color: #181a25;
    }

    .sidebar i {
      font-size: 17px;
      margin-right: 12px;
      margin-left: 5px;
    }

    .sidebar span:not(.notif-badge) {
      display: none;
    }

    .sidebar:hover span {
      display: inline;
    }

   .sidebar a .notif-badge {
  position: absolute;
  top: 4px;
  right: 12px;
  background: red;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 50%;
  z-index: 9999;
  transition: all 0.2s ease-in-out;
}

.sidebar:hover a .notif-badge {
  right: 18px;
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

    .notification-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.7);
      justify-content: center;
      align-items: center;
      z-index: 1001;
    }

    .notification-box {
      background: #181a25;
      color: white;
      padding: 25px;
      border-radius: 12px;
      width: 500px;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
    }

    .notification-box h2 {
      text-align: center;
      margin-top: 0;
    }

    .close-notif-modal {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
      color: white;
      background: none;
      border: none;
    }

    .notif-item {
      background: #0d121e;
      margin-bottom: 15px;
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #2a2f45;
    }

    .notif-item strong {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
    }

    #sitInFormContainer {
      display: none;
      margin-left: 100px;
      padding: 30px;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-links">
    <a href="admin-home.php"><i class="fas fa-home"></i><span>Home</span></a>
    <a href="#" onclick="openModal()"><i class="fas fa-search"></i><span>Search</span></a>
    <a href="admin-students.php"><i class="fas fa-users"></i><span>Students</span></a>
    <a href="admin-announcements.php"><i class="fas fa-bullhorn"></i><span>Announcement</span></a>
    <a href="admin-current.php"><i class="fas fa-calendar-check"></i><span>Current Sit-In</span></a>
    <a href="admin-records.php"><i class="fas fa-folder"></i><span>Sit-In Records</span></a>
    <a href="admin-reports.php"><i class="fas fa-file-alt"></i><span>Sit-In Reports</span></a>
    <a href="admin-feedback.php"><i class="fas fa-comment-dots"></i><span>Feedbacks</span></a>
    <a href="admin-resources.php"><i class="fas fa-book"></i><span>Resources</span></a>
    <a href="admin-reservations.php"><i class="fas fa-calendar-alt"></i><span>Reservations</span></a>
    <a href="admin-laboratories.php"><i class="fas fa-flask"></i><span>Lab Schedule</span></a>
    <a href="admin-leaderboard.php"><i class="fas fa-trophy"></i><span>Leaderboard</span></a>
    <a href="#" onclick="openNotificationModal()" style="position: relative;">
      <i class="fas fa-bell"></i><span>Notifications</span>
      <?php if ($notif_count > 0): ?>
        <span class="notif-badge"><?php echo $notif_count; ?></span>
      <?php endif; ?>
    </a>
  </div>

  <div class="sidebar-logout" style="margin-top:80px; padding-left: 5px; margin-bottom: auto;">
    <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
  </div>
</div>  

<!-- Search Modal (Unchanged) -->
<div class="search-overlay" id="searchModal">
  <div class="search-box">
    <button class="close-search-modal" onclick="closeModal()">&times;</button>
    <h2>Search Student</h2>
    <input type="text" id="searchInput" placeholder="Enter Student ID or Name">
    <button onclick="searchStudent()"><i class="fas fa-search"></i></button>
  </div>
</div>

<!-- Notification Modal (NEW) -->
<div id="notificationModal" class="notification-modal">
  <div class="notification-box">
    <button class="close-notif-modal" onclick="closeNotificationModal()">&times;</button>
    <h2>Notifications</h2>
    <div id="notificationContent">Loading...</div>
  </div>
</div>

<div id="sitInFormContainer"></div>

<script>
function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "../includes/logout.php";
  }
}

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
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "query=" + encodeURIComponent(query)
  })
  .then(res => res.text())
  .then(html => {
    const container = document.getElementById("sitInFormContainer");
    container.innerHTML = html;
    container.style.display = "block";
    closeModal();
  })
  .catch(error => {
    console.error("Error:", error);
    alert("Error fetching student info.");
  });
}

// 🛎 Notification Modal Logic
function openNotificationModal() {
  document.getElementById('notificationModal').style.display = 'flex';

  fetch("../includes/fetch-reservations.php")
    .then(res => res.text())
    .then(html => {
      document.getElementById("notificationContent").innerHTML = html;
    })
    .catch(err => {
      document.getElementById("notificationContent").innerHTML = "Failed to load.";
    });
}

function closeNotificationModal() {
  document.getElementById('notificationModal').style.display = 'none';
}
</script>
</body>
</html>
