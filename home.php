<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            padding: 20px 20px 10px;
            text-align: center;
        }

        .header img {
            width: 60px;
            height: auto;
            vertical-align: middle;
            margin-right: 12px;
        }

        .header h1 {
            display: inline-block;
            vertical-align: middle;
            font-size: 30px;
            font-weight: 700;
            margin: 0;
        }

        .content {
            margin-left: 270px;
            padding: 20px 10px;
            box-sizing: border-box;
            color: white;
        }

        .announcements {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
            border-radius: 15px;
            border: 2px solid white;
            background-color: #0d121e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            width: 40%;
        }

        .announcements h3 {
            color: white;
            font-weight: bold;
            text-align: left;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .announcements h3 i {
            margin-right: 8px;
        }

        .card {
            background-color: #212b40;
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            color: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .card p {
            color: white;
            margin: 8px 0 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .date-posted {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 6px;
        }

        @media screen and (max-width: 768px) {
            .announcements {
                width: 90%;
            }
        }

        @media screen and (max-width: 480px) {
            .announcements {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="assets/ccs.png" alt="CCS Logo">
        <h1>CCS Sit-In Monitoring System</h1>
    </div>

    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="announcements">
            <h3><i class="fas fa-bullhorn"></i>Announcements</h3>

            <?php
            $conn = new mysqli("localhost", "root", "", "sysarch");
            if ($conn->connect_error) {
                echo "<p>Error connecting to database.</p>";
            } else {
                $sql = "SELECT announcement_text, created_at, created_by FROM announcements ORDER BY created_at DESC LIMIT 5";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<div class="date-posted">Posted on: ' . date("F j, Y - g:i A", strtotime($row['created_at'])) . ' by ' . htmlspecialchars($row['created_by']) . '</div>';
                        echo '<p>' . nl2br(htmlspecialchars($row['announcement_text'])) . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No announcements available.</p>';
                }

                $conn->close();
            }
            ?>
        </div>
    </div>
</body>
</html>
