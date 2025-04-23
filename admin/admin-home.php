<?php
session_start();
include '../includes/db-connection.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}

// Fetch the total number of registered students (excluding admin)
$sql_users = "SELECT COUNT(*) as total_users FROM users WHERE role != 'admin' OR role IS NULL";
$result_users = $conn->query($sql_users);
$total_users = ($result_users->num_rows > 0) ? $result_users->fetch_assoc()['total_users'] : 0;


// Fetch the number of currently active students
$sql_current_sitin = "SELECT COUNT(*) as current_sitin FROM sit_in_records WHERE status = 'Active'";
$result_current_sitin = $conn->query($sql_current_sitin);
$current_sitin = ($result_current_sitin->num_rows > 0) ? $result_current_sitin->fetch_assoc()['current_sitin'] : 0;


// Fetch total sit-ins ever recorded
$sql_total_sitin = "SELECT COUNT(*) as total_sitin FROM sit_in_records";
$result_total_sitin = $conn->query($sql_total_sitin);
$total_sitin = ($result_total_sitin->num_rows > 0) ? $result_total_sitin->fetch_assoc()['total_sitin'] : 0;

// Fetch sit-in purposes and their counts
$sql_purpose = "SELECT purpose, COUNT(*) as count FROM sit_in_records GROUP BY purpose";
$result_purpose = $conn->query($sql_purpose);

$purposes = [];
$counts = [];
while ($row = $result_purpose->fetch_assoc()) {
    $purposes[] = $row['purpose'];
    $counts[] = $row['count'];
}

$conn->close();
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
        body { margin: 0; font-family: 'Inter', sans-serif; display: flex; background-color: #0d121e; color: #ffffff; }
        .main-content { margin-left: 80px; padding: 20px; transition: margin-left 0.3s; flex: 1; }
        .sidebar:hover ~ .main-content { margin-left: 180px; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 2px solid #333; }
        header h1 { margin: 0; color: white; font-size: 30px; }
        .overview { display: flex; justify-content: center; gap: 20px; margin-top: 30px; }
        .card { width: 220px; padding: 15px; background-color: #212b40; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); text-align: center; color: white; }
        .card-header { display: flex; align-items: center; justify-content: center; gap: 8px; text-align: center; }
        .card-header i { font-size: 18px; color: white; }
        .overview .card h3 { margin-bottom: 10px; font-weight: 400; font-size: 15px; }
        .overview .card p { margin: 0; font-size: 25px; font-weight: bold; }
        .chart-container { margin-top: 20px; text-align: left; }
        .chart-container h2 { color: white; font-size: 25px; margin-bottom: 20px; }
        #purposeChart { width: 100% !important; height: 500px !important; max-width: 100%; display: block; margin: 0; background-color: #1a2336; padding: 10px; border-radius: 10px; }
</style>

</head>
<body>
    <?php include '../includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Admin - Dashboard</h1>
        </header>

        <section class="overview">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-graduate"></i> 
                    <h3>Students Registered</h3>
                </div>
                <p class="number"><?= $total_users; ?></p>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chair"></i>
                    <h3>Current Sit-In</h3>
                </div>
                <p class="number"><?= $current_sitin; ?></p>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    <h3>Total Sit-In</h3>
                </div>
                <p class="number"><?= $total_sitin; ?></p>
            </div>
        </section>

        <section class="chart-container">
            <h2>Statistics</h2>
            <canvas id="purposeChart"></canvas>
        </section>
    </div>

    <script>
        var ctx = document.getElementById('purposeChart').getContext('2d');
        var purposeChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: <?= json_encode($purposes); ?>,
                datasets: [{
                    label: 'Number of Students',
                    data: <?= json_encode($counts); ?>,
                    backgroundColor: [
                        '#3A7CA5',
                        '#1E5F74',
                        '#144272',
                        '#2B4865',
                        '#0F3460'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#fff' }
                    },
                    x: {
                        ticks: { color: '#fff' }
                    }
                }
            }
        });
    </script>
</body>
</html>
