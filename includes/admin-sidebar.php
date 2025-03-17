<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            width: 80px;
            height: 100vh;
            background-color: #0d121e;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: width 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            overflow: hidden;
            align-items: center; /* Centers items when collapsed */
            padding-top: 20px;
        }

        .sidebar:hover {
            width: 250px;
            align-items: flex-start; /* Aligns items normally when expanded */
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .admin-label {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
            margin-left: 20px;
        }

        .sidebar:hover .admin-label {
            opacity: 1;
        }

        .sidebar-links,
        .sidebar-logout {
            width: 100%;
        }

        .sidebar-links a,
        .sidebar-logout a {
            text-decoration: none;
            color: whitesmoke;
            padding: 15px;
            display: flex;
            flex-direction: column; /* Stacks icons and text vertically */
            align-items: center; /* Centers content */
            transition: background-color 0.3s;
            white-space: nowrap;
        }

        .sidebar:hover .sidebar-links a,
        .sidebar:hover .sidebar-logout a {
            flex-direction: row; /* Aligns items horizontally on hover */
            align-items: center;
        }

        .sidebar-links a i,
        .sidebar-logout a i {
            font-size: 18px;
            text-align: center;
        }

        .sidebar-links a span,
        .sidebar-logout a span {
            opacity: 0;
            transition: opacity 0.3s;
            margin-left: 10px;
        }

        .sidebar:hover .sidebar-links a span,
        .sidebar:hover .sidebar-logout a span {
            opacity: 1;
        }

        .sidebar-links a:hover,
        .sidebar-logout a:hover {
            background-color: #181a25;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Admin Profile Image -->
        <img src="../assets/admin-icon.png" alt="Admin Icon" class="sidebar-logo">
        <div class="admin-label">ADMIN</div> <!-- Hidden until hover -->

        <!-- Sidebar Links -->
        <div class="sidebar-links">
            <a href="#"><i class="fas fa-search"></i><span>Search Student</span></a>
            <a href="students.php"><i class="fas fa-users"></i><span>List of Students</span></a>
            <a href="announcement.php"><i class="fas fa-bullhorn"></i><span>Announcement</span></a>
            <a href="current-sit-in.php"><i class="fas fa-calendar-check"></i><span>Current Sit-In</span></a>
        </div>
        
        <!-- Logout Button -->
        <div class="sidebar-logout">
            <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>
    
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "includes/logout.php";
            }
        }
    </script>
</body>
</html>
