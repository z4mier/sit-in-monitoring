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
$middlename = isset($user['middlename']) ? htmlspecialchars($user['middlename']) : '';
$lastname = htmlspecialchars($user['lastname']);
$username = htmlspecialchars($user['username']);

// Combine names to display full name
$fullname = $firstname . ' ' . $middlename . ' ' . $lastname;

// Database connection to fetch additional user details
$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user_data = $result->fetch_assoc();
    $id_no = htmlspecialchars($user_data['id_no']);
    $yr_level = htmlspecialchars($user_data['yr_level']);
    $course = htmlspecialchars($user_data['course']);
    $profile_picture = htmlspecialchars($user_data['profile_picture']);
    $address = isset($user_data['address']) ? htmlspecialchars($user_data['address']) : '';  // Added check for address
    $email = htmlspecialchars($user_data['email']);
    $remaining_sessions = 30;  // Default remaining session
} else {
    echo "User data not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CCS Sit-In Monitoring System</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0d121e;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .header {
            color: white;
            padding: 15px 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .content {
            flex-grow: 1;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            padding: 20px;
            border-radius: 20px;
            border: 2px solid white; /* Adds a white border */
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); /* Optional, adds a soft glow */
            max-width: 500px;
            width: 100%; /* Ensure the card takes full width up to max-width */
            position: relative; /* For button positioning */
        }
        .card h3 {
            text-align: center;
            color: white; /* Highlighted header color */
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px auto; 
            display: block;
        }
        .card p, h3 {
            text-align: left;
            color: white;
        }
        .edit-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 25px;
            cursor: pointer;
        }
        .edit-btn:hover {
            color: #00bcd4; 
        }
         .detail-item {
            background-color: #1a1f2e;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .detail-item p {
            margin: 0;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CCS Sit-In Monitoring System</h1>
    </div>
    <?php include 'includes/sidebar.php'; ?>
    <div class="content">
        <div class="card">
            <button class="edit-btn" onclick="window.location.href='edit.php'">
                &#9998;
            </button>
            <img src="<?php echo $profile_picture ? $profile_picture : 'assets/icon.png'; ?>" alt="Profile Picture" class="profile-img">
            <h3>Personal Information</h3>
            
                <div class="detail-item">
                    <p><strong>ID Number:</strong> <?php echo $id_no; ?></p>
                </div>
                <div class="detail-item">
                    <p><strong>Full Name:</strong> <?php echo $fullname; ?></p>  <!-- Display full name -->
                </div>
                <div class="detail-item">
                    <p><strong>Year Level:</strong> <?php echo $yr_level; ?></p>
                </div>
                <div class="detail-item">
                    <p><strong>Course:</strong> <?php echo $course; ?></p>
                </div>
                <div class="detail-item">
                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                </div>
                <div class="detail-item">
                    <p><strong>Address:</strong> <?php echo $address; ?></p>
                </div>
                <div class="detail-item">
                    <p><strong>Remaining Sessions:</strong> <?php echo $remaining_sessions; ?></p>  <!-- Display default remaining session -->
                </div>
        </div>
    </div>
</body>
</html>