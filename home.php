<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Retrieve user details
$user = $_SESSION['user'];
$firstname = htmlspecialchars($user['firstname']);
$lastname = htmlspecialchars($user['lastname']);
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring System</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #0d121e;
        margin: 0;
        padding: 0;
    }

    .header {
        color: white;
        padding: 15px 20px;
        text-align: center;
    }

    .content {
        margin-left: 270px;
        padding: 10px;
        box-sizing: border-box;
        color: white;
        
    }

        .events-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px; 
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        width: 45%; 
        box-sizing: border-box; 
        background-color: #0d121e;
        border: 2px solid white; /* Adds a white border */
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); /* Optional, adds a soft glow */
    }

    .events-section h3 {
        flex-basis: 100%; 
        color: white;;
        font-weight: bold;
        text-align: left;
        margin-bottom: 10px;
    }

    .card {
    background-color: #212b40;
    padding: 15px;
    border-radius: 10px;
    flex: 1 1 calc(30% - 20px);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    color: white;
    border: 1px solid rgba(0, 0, 0, 0.1);
    min-width: 200px; 
    padding-right: 20px; 
}
    .card h4 {
        margin: 0;
        color: white;
        font-size: 16px;
        font-weight: bold;
    }

    .card p {
        color: white;
        margin: 10px 0 0;
        font-size: 14px;
        line-height: 1.4;
    }

    @media screen and (max-width: 768px) {
    .card {
        flex: 1 1 calc(45% - 20px); /* Two cards per row on smaller screens */
    }
}

    @media screen and (max-width: 480px) {
    .card {
        flex: 1 1 100%;
    }
}
</style>
</head>
<body>
    <div class="header">
        <h1>CCS Sit-In Monitoring System</h1>
    </div>
    <?php include 'includes/sidebar.php'; ?>
    <div class="content">
        <h2>Welcome, <?php echo $firstname . " " . $lastname; ?>!</h2>

        <!-- Upcoming Events -->
        <div class="events-section">
            <h3>Upcoming Events</h3>

            <div class="card">
                <h4>ICT Congress</h4>
                <p>Starting next week, updated laboratory rules and regulations will take effect. Please make sure to review the latest guidelines before attending sessions.</p>
            </div> 
        </div>
        
    </div>
</body>
</html>