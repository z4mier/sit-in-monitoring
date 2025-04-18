<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Retrieve user details
$user = $_SESSION['user'];
$username = htmlspecialchars($user['username']);

$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user_data = $result->fetch_assoc();
    $profile_picture = htmlspecialchars($user_data['profile_picture']);
} else {
    $profile_picture = 'assets/icon.png'; // Default profile picture
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

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
            align-items: stretch;
            width: 250px; /* Fixed width */
            height: 100vh;
            background-color: #0d121e;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            box-sizing: border-box;
            text-align: left;
            border-right: 3px solid #181a25;
        }

        .sidebar .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 20px auto 10px auto;
        }

        .sidebar .user-info {
            text-align: center;
            margin-bottom: 20px;
            opacity: 1; /* Always visible */
        }

        .sidebar .user-info .username {
            font-size: 16px;
            font-weight: bold;
        }

        .sidebar .user-info .status {
            font-size: 14px;
            color: #00ff00;
        }

        .sidebar-links {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 10px;
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
        }

        .sidebar a:hover {
            background-color: #181a25;
        }

        .sidebar i {
            font-size: 18px;
            margin-right: 10px;
        }

        .sidebar span {
            display: inline;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Profile Picture -->
        <img src="<?php echo $profile_picture ? $profile_picture : 'assets/icon.png'; ?>" alt="Profile Image" class="profile-image">
        
        <!-- User Info -->
        <div class="user-info">
            <div class="username"><?php echo $username; ?></div>
            <div class="status">Online</div>
        </div>
        
        <!-- Sidebar Links -->
        <div class="sidebar-links">
            <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
            <a href="lab-resources.php"><i class="fas fa-desktop"></i><span>Lab Resources</span></a>
            <a href="lab-schedule.php"><i class="fas fa-clock"></i><span>Lab Schedule</span></a>
            <a href="lab-rules.php"><i class="fas fa-flask"></i><span>Lab Rules & Regulation</span></a>
            <a href="history.php"><i class="fas fa-history"></i><span>History</span></a>
            <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
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
