<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php"); 
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            background-color: #0d121e;
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
            border-bottom: 2px solid #ddd;
        }

        header h1 {
            margin: 0;
            color: white;
            font-size: 30px;
        }

        .overview {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            width: 220px; 
            padding: 15px;
            background-color: #212b40;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .card-header i {
            font-size: 18px;
            color: white;
        }

        .overview .card h3 {
            margin-bottom: 10px;
            color: white;
            font-weight: 400;
            font-size: 15px;
        }

        .overview .card p {
            margin: 0;
            font-size: 25px;
            font-weight: bold;
            color: white;
        }

        .chart-container {
            margin-top: 40px;
        }

        .chart-container h2 {
            color: white; 
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
        </header>

        <!-- Overview Section -->
        <section class="overview">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-graduate"></i> 
                    <h3>Students Registered</h3>
                </div>
                <p class="number">100</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chair"></i>
                    <h3>Current Sit-In</h3>
                </div>
                <p class="number">8</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    <h3>Total Sit-In</h3>
                </div>
                <p class="number">5</p>
            </div>
        </section>

        <section class="chart-container">
            <h2>Statistics</h2>
            <canvas id="languageChart"></canvas>
        </section>
    </div>
</body>
</html>
