<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? [];
$username = htmlspecialchars($user['username'] ?? 'Guest');
$id_no = $user['id_no'] ?? 0;


$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get profile picture
$sql = "SELECT profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile_picture = 'assets/icon.png';

if ($result->num_rows == 1) {
    $user_data = $result->fetch_assoc();
    if (!empty($user_data['profile_picture'])) {
        $profile_picture = htmlspecialchars($user_data['profile_picture']);
    }
}

// Fetch notifications from DB
$notif_sql = "SELECT updated_at FROM reservations WHERE id_no = ? AND status = 'Approved' ORDER BY updated_at DESC LIMIT 5";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $id_no);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result();

$notifications = [];
while ($row = $notif_result->fetch_assoc()) {
    $time_ago = floor((time() - strtotime($row['updated_at'])) / 60);
    $notifications[] = [
        'name' => 'Admin',
        'action' => 'accepted your reservation',
        'time' => $time_ago > 0 ? "$time_ago min ago" : "just now"
    ];
}

$unreadNotificationsCount = count($notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; }
        .sidebar { display: flex; flex-direction: column; justify-content: space-between; width: 250px; height: 100vh; background-color: #0d121e; color: white; position: fixed; top: 0; left: 0; padding: 20px 0; box-sizing: border-box; border-right: 3px solid #181a25; z-index: 100; }
        .profile-image { width: 100px; height: 100px; border-radius: 50%; margin: 20px auto 10px auto; }
        .user-info { text-align: center; margin-bottom: 20px; }
        .username { font-size: 16px; font-weight: bold; }
        .status { font-size: 14px; color: #00ff00; }
        .sidebar-links { flex-grow: 1; display: flex; flex-direction: column; gap: 10px; padding: 0 10px; }
        .sidebar-logout { padding: 0 10px; }
        .sidebar a { color: whitesmoke; text-decoration: none; display: flex; align-items: center; padding: 10px 10px; border-radius: 10px; transition: 0.3s; }
        .sidebar a:hover { background-color: #181a25; }
        .sidebar i { font-size: 18px; margin-right: 10px; }
        .notification-badge { position: relative; }
        .notif-dot {
        position: absolute;
        top: 4px;
        left: 20px;
        width: 10px;
        height: 10px;
        background-color: #ff4d4d;
        border-radius: 50%;
        display: inline-block;
    }
        .notification-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%;
            height: 100%; background: rgba(0, 0, 0, 0.7); justify-content: center;
            align-items: center; z-index: 1000;
        }

        .notification-modal {
            background-color: #121624; width: 450px; max-width: 90%; border-radius: 10px;
            overflow: hidden; position: relative; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .notification-header {
            padding: 20px; text-align: center; font-size: 24px; font-weight: bold;
            border-bottom: 1px solid #1e2230; position: relative;
        }

        .notification-close {
            position: absolute; top: 20px; right: 20px; font-size: 24px;
            background: none; border: none; color: white; cursor: pointer;
        }

        .notification-content { max-height: 400px; overflow-y: auto; padding: 0; }
        .notification-item {
            display: flex; align-items: center; padding: 15px 20px;
            border-bottom: 1px solid #1e2230; transition: background-color 0.2s;
        }

        .notification-item:hover { background-color: #1e2230; }

        .notification-profile {
            width: 50px; height: 50px; border-radius: 50%; overflow: hidden;
            margin-right: 15px; flex-shrink: 0;
        }

        .notification-profile img { width: 100%; height: 100%; object-fit: cover; }
        .notification-details { flex-grow: 1; }
        .notification-text { margin-bottom: 5px; }
        .notification-text strong { font-weight: bold; }
        .notification-time { font-size: 12px; color: #aaa; }
        .notification-empty { padding: 30px; text-align: center; color: #999; }
    </style>
</head>
<body>
<div class="sidebar">
    <img src="<?php echo $profile_picture; ?>" alt="Profile Image" class="profile-image">
    <div class="user-info">
        <div class="username"><?php echo $username; ?></div>
        <div class="status">Online</div>
    </div>
    <div class="sidebar-links">
        <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="lab-resources.php"><i class="fas fa-desktop"></i><span>Lab Resources</span></a>
        <a href="lab-schedule.php"><i class="fas fa-clock"></i><span>Lab Schedule</span></a>
        <a href="lab-rules.php"><i class="fas fa-flask"></i><span>Lab Rules</span></a>
        <a href="history.php"><i class="fas fa-history"></i><span>History</span></a>
        <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
       <a href="#" onclick="openNotificationModal()" style="position: relative;">
    <i class="fas fa-bell"></i>
    <?php if ($unreadNotificationsCount > 0): ?>
        <span class="notif-dot"></span>
    <?php endif; ?>
    <span>Notifications</span>
</a>

    </div>
    <div class="sidebar-logout">
        <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<!-- Modal -->
<div class="notification-overlay" id="notificationModal">
    <div class="notification-modal">
        <div class="notification-header">
            Notifications
            <button class="notification-close" onclick="closeNotificationModal()">&times;</button>
        </div>
        <div class="notification-content" id="notificationContent">
            <?php if (count($notifications) > 0): ?>
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item">
                        <div class="notification-profile">
                            <img src="assets/admin.png" alt="Admin">
                        </div>
                        <div class="notification-details">
                            <div class="notification-text"><strong><?php echo $notif['name']; ?></strong> <?php echo $notif['action']; ?></div>
                            <div class="notification-time"><?php echo $notif['time']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notification-empty">No notifications to display</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "includes/logout.php";
        }
    }

    function openNotificationModal() {
        document.getElementById('notificationModal').style.display = 'flex';

        const container = document.getElementById('notificationContent');
        container.innerHTML = `
            <div class="notification-empty" style="color: #aaa; text-align: center; padding: 20px;">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
        `;

        fetch('includes/fetch-notifications.php')
            .then(response => response.json())
            .then(data => {
                container.innerHTML = ''; // Clear loading

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(n => {
                        container.innerHTML += `
                            <div class="notification-item">
                                <div class="notification-profile">
                                    <img src="assets/uc.png" alt="Admin">
                                </div>
                                <div class="notification-details">
                                    <div class="notification-text">
                                        <strong>Admin</strong> ${n.status.toLowerCase()} your reservation
                                    </div>
                                    <div class="notification-time">${n.timestamp}</div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    container.innerHTML = `
                        <div class="notification-empty">No notifications to display</div>
                    `;
                }
            })
            .catch(error => {
                console.error("Notification fetch failed:", error);
                container.innerHTML = `
                    <div class="notification-empty">Failed to load notifications</div>
                `;
            });
    }

    function closeNotificationModal() {
        document.getElementById('notificationModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('notificationModal');
        if (event.target === modal) {
            closeNotificationModal();
        }
    };
</script>


</body>
</html>
