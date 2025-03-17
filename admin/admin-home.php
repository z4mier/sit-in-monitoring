<?php
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php"); // Redirect to login page if not an admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Add your external CSS file here -->
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
        }

        .main-content {
            margin-left: 80px; /* Matches the default sidebar width */
            padding: 20px;
            transition: margin-left 0.3s;
            flex: 1;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 250px; /* Matches expanded sidebar width */
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f4;
            padding: 20px;
            border-bottom: 2px solid #ddd;
        }

        header h1 {
            margin: 0;
            color: #0d121e;
        }

        .overview {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .overview .card {
            flex: 1;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .overview .card h3 {
            margin-bottom: 10px;
            color: #0d121e;
        }

        .overview .card p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #3498db;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../includes/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header>
            <h1>Welcome, Admin!</h1>
        </header>

        <!-- Overview Section -->
        <section class="overview">
            <div class="card">
                <h3>Total Students</h3>
                <p>1200</p>
            </div>
            <div class="card">
                <h3>Active Announcements</h3>
                <p>8</p>
            </div>
            <div class="card">
                <h3>Ongoing Sit-Ins</h3>
                <p>5</p>
            </div>
        </section>

        <!-- Additional Content -->
        <section class="content">
            <h2>Dashboard Content</h2>
            <p>Here you can manage students, view reports, and handle announcements.</p>
        </section>
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
