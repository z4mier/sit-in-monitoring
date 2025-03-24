<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('');
        
        .sidebar {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: stretch; 
            width: 80px;
            height: 100vh;
            background-color: #0d121e;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            box-sizing: border-box;
            text-align: left;
            transition: width 0.3s;
            border-right: 3px solid #181a25;
        }

        .sidebar:hover {
            width: 180px;
        }

        .sidebar .profile-image {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            margin: 20px auto 10px auto;
            transition: width 0.3s, height 0.3s;
        }

        .sidebar .user-info {
            text-align: center;
            margin: 0;
            opacity: 0;
            transition: opacity 0.3s;
            margin-bottom: 20px;
        }

        .sidebar:hover .user-info {
            opacity: 1;
        }


        .sidebar:hover .admin-label {
            display: block;
        }

        .sidebar-links {
            flex-grow: 1; 
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 10px; 
            margin-top: 20px; 
        }

        .sidebar-logout {
            padding: 0 10px; 
        }

        .sidebar a {
            color: whitesmoke;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 10px; 
            transition: background-color 0.3s;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
        }

        .sidebar a:hover {
            background-color: #181a25;
        }

        .sidebar i {
            font-size: 17px;
            margin-right: 10px;
            margin-left: 10px;
            transition: margin 0.3s, font-size 0.3s;
        }

        .sidebar span {
            display: none;
            white-space: nowrap;
            transition: display 0.3s;
        }

        .sidebar:hover .profile-image { 
            width: 100px; 
            height: 100px;
        }

        .sidebar:hover span {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        
        
        <!-- Sidebar Links -->
        <div class="sidebar-links">
            <a href="admin-home.php"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="admin-students.php"><i class="fas fa-users"></i><span>Students</span></a>
            <a href="admin-announcements.php"><i class="fas fa-bullhorn"></i><span>Announcement</span></a>
            <a href="admin-current.php"><i class="fas fa-calendar-check"></i><span>Current Sit-In</span></a>
        </div>
        
        <!-- Logout Button -->
        <div class="sidebar-logout">
            <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "../includes/logout.php";
            }
        }
    </script>
</body>
</html>