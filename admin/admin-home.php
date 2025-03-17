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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            background-color: #0d121e;
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
            color: white;
            padding: 20px;
            border-bottom: 2px solid #ddd;
        }

        header h1 {
            margin: 0;
            color: white;
        }

        .overview {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .overview .card {
            flex: 1;
            padding: 20px;
            background-color: #212b40;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .overview .card h3 {
            margin-bottom: 10px;
            color: white;
            font-weight: normal;
            font-size: 18px;
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header>
            <h1>Dashboard</h1>
        </header>

        <!-- Overview Section -->
        <section class="overview">
            <div class="card">
                <h3>Students Registered</h3>
                <p>1200</p>
            </div>
            <div class="card">
                <h3>Current Sit-In</h3>
                <p>8</p>
            </div>
            <div class="card">
                <h3>Total Sit-In</h3>
                <p>5</p>
            </div>
        </section>

        <!-- Chart Section -->
        <section class="chart-container">
            <h2>Statistics</h2>
            <canvas id="languageChart"></canvas>
        </section>
    </div>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "../includes/logout.php";
            }
        }

        // Sample data for the chart
        const languageData = {
            labels: ['Python', 'JavaScript', 'Java', 'C++', 'PHP', 'Ruby'],
            datasets: [{
                label: 'Number of Students',
                data: [50, 30, 20, 10, 5, 2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Config for the chart
        const config = {
            type: 'bar',
            data: languageData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white' 
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                }
            }
        };

        // Render the chart
        const languageChart = new Chart(
            document.getElementById('languageChart'),
            config
        );
    </script>
</body>
</html>