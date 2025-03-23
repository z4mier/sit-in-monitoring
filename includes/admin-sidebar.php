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
            flex-direction: column; /* Stack items vertically */
            justify-content: space-between; /* Push content to top and bottom */
            align-items: stretch; /* Align items to the left */
            width: 80px;
            height: 100vh;
            background-color: #0d121e;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            box-sizing: border-box;
            text-align: left; /* Left-align the text */
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
            margin: 20px auto 10px auto; /* Adjusted margin to move closer to username */
            transition: width 0.3s, height 0.3s;
        }

        .sidebar .user-info {
            text-align: center;
            margin: 0; /* Removed margin to move closer to profile image */
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
            flex-grow: 1; /* Makes the links section take remaining space */
            display: flex;
            flex-direction: column;
            gap: 10px; /* Adds space between links */
            padding: 0 10px; /* Add left-right padding for spacing */
            margin-top: 20px; /* Adds space between admin label and first link */
        }

        .sidebar-logout {
            padding: 0 10px; /* Ensure alignment with other items */
        }

        .sidebar a {
            color: whitesmoke;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 10px; /* Ensure consistent spacing for all links */
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
            <a href="announcement.php"><i class="fas fa-bullhorn"></i><span>Announcement</span></a>
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