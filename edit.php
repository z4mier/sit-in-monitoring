<?php 
session_start(); 

// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) { 
    header("Location: index.php"); 
    exit(); 
} 

// Ensure 'uploads' directory exists
if (!file_exists('uploads')) { 
    mkdir('uploads', 0777, true); 
} 

// Retrieve user details from the session
$user = $_SESSION['user']; 
$firstname = htmlspecialchars($user['firstname']); 
$lastname = htmlspecialchars($user['lastname']); 
$username = htmlspecialchars($user['username']); 

// Database connection to fetch additional user details
$conn = new mysqli("localhost", "root", "", "sysarch"); 
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
} 

// Fetch user details
$sql = "SELECT * FROM users WHERE username = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("s", $username); 
$stmt->execute(); 
$result = $stmt->get_result(); 

if ($result->num_rows == 1) { 
    $user_data = $result->fetch_assoc(); 
    $id_no = htmlspecialchars($user_data['id_no']); 
    $middlename = htmlspecialchars($user_data['middlename']); 
    $yr_level = htmlspecialchars($user_data['yr_level']); 
    $course = htmlspecialchars($user_data['course']); 
    $profile_picture = htmlspecialchars($user_data['profile_picture']); 
    $email = htmlspecialchars($user_data['email']); 
    $address = htmlspecialchars($user_data['address']); 
    $remaining_sessions = isset($user_data['remaining_sessions']) ? $user_data['remaining_sessions'] : 30; // Default value
} else { 
    echo "User data not found."; 
    exit(); 
} 

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $new_firstname = htmlspecialchars($_POST['firstname']); 
    $new_middlename = htmlspecialchars($_POST['middlename']); 
    $new_lastname = htmlspecialchars($_POST['lastname']); 
    $new_yr_level = htmlspecialchars($_POST['yr_level']); 
    $new_course = htmlspecialchars($_POST['course']); 
    $new_email = htmlspecialchars($_POST['email']); 
    $new_address = htmlspecialchars($_POST['address']); 

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) { 
        $target_dir = "uploads/"; 
        $file_name = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9\.\-\_]/", "", basename($_FILES["profile_picture"]["name"]));
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) { 
            $profile_picture = $target_file; 
    
            // Update session
            $_SESSION['user']['profile_picture'] = $profile_picture; 
        } else { 
            echo "Error uploading file. Please check folder permissions.";
        } 
    } else {
        // Retain existing profile picture if no new image is uploaded
        $profile_picture = htmlspecialchars($user_data['profile_picture']);
    }

    // Update user information in the database
    $update_sql = "UPDATE users SET firstname=?, middlename=?, lastname=?, yr_level=?, course=?, profile_picture=?, email=?, address=? WHERE username=?"; 
    $update_stmt = $conn->prepare($update_sql); 
    $update_stmt->bind_param("sssssssss", $new_firstname, $new_middlename, $new_lastname, $new_yr_level, $new_course, $profile_picture, $new_email, $new_address, $username); 

    if ($update_stmt->execute()) { 
        // Update session variables
        $_SESSION['user']['firstname'] = $new_firstname; 
        $_SESSION['user']['middlename'] = $new_middlename; 
        $_SESSION['user']['lastname'] = $new_lastname; 
        $_SESSION['user']['yr_level'] = $new_yr_level; 
        $_SESSION['user']['course'] = $new_course; 
        $_SESSION['user']['email'] = $new_email; 
        $_SESSION['user']['address'] = $new_address; 

        // Redirect to profile page
        header("Location: profile.php"); 
        exit(); 
    } else { 
        echo "Error updating profile: " . $conn->error;
    } 
}
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
            padding: 15px 15px;
            text-align: center;
        }
        .card {
            color: white;
            padding: 20px;
            margin: 20px auto;
            border-radius: 20px;
            border: 2px solid white;
            max-width: 500px;
            position: relative; /* For positioning the cancel button */
        }
        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 10px;
            width: 100%;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 16px;
            color: white;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 10px;
            background-color: #1a1f2e;
            color: white;
            outline: none;
            box-sizing: border-box;
            border: none;
        }

        .save-btn {
            display: block;
            width: 30%;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            background-color: #0d121e;
            color: white;
            border: 1px solid white;
            border-radius: 10px;
            margin: 20px auto 0;
            cursor: pointer;
            text-align: center;
        }
        .save-btn:hover {
            background-color: white;
            color: #181a25; 
        }
        .change-btn {
            padding: 8px 12px;
            font-size: 14px;
            color: white;
            background-color: #0d121e;
            border: 1px solid white;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }
        .change-btn:hover {
            background-color: white;
            color: #181a25; 
        }
        .cancel-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 25px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            color: #00bcd4;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>CCS Sit-In Monitoring System</h1>
    <?php include 'includes/sidebar.php'; ?>
</div>
<div class="card">
    <button class="cancel-btn" onclick="window.location.href='profile.php'">&times;</button>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <!-- Profile Picture Section -->
        <div class="profile-section">
            <img id="profilePreview" src="<?php echo $profile_picture ? $profile_picture : 'assets/icon.png'; ?>" alt="Profile Picture" class="profile-img">
            <input type="file" id="profileInput" name="profile_picture" style="display: none;" accept="image/*">
            <button type="button" class="change-btn" onclick="triggerFileInput()">Change</button>
        </div>

        <!-- User Details Section -->
        <div class="form-group">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>">
        </div>
        <div class="form-group">
            <label for="middlename">Middle Name:</label>
            <input type="text" id="middlename" name="middlename" value="<?php echo $middlename; ?>">
        </div>
        <div class="form-group">
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>">
        </div>
        <div class="form-group">
            <label for="yr_level">Year Level:</label>
            <select id="yr_level" name="yr_level">
                <option value="1" <?php if ($yr_level == '1') echo 'selected'; ?>>1</option>
                <option value="2" <?php if ($yr_level == '2') echo 'selected'; ?>>2</option>
                <option value="3" <?php if ($yr_level == '3') echo 'selected'; ?>>3</option>
                <option value="4" <?php if ($yr_level == '4') echo 'selected'; ?>>4</option>
            </select>
        </div>
        <div class="form-group">
            <label for="course">Course:</label>
            <select id="course" name="course">
                <option value="BSIT" <?php if ($course == 'BSIT') echo 'selected'; ?>>BSIT</option>
                <option value="BSCS" <?php if ($course == 'BSCS') echo 'selected'; ?>>BSCS</option>
                <option value="BSCpE" <?php if ($course == 'BSCpE') echo 'selected'; ?>>BSCpE</option>
                <option value="BSED" <?php if ($course == 'BSED') echo 'selected'; ?>>BSED</option>
                <option value="BSHM" <?php if ($course == 'BSHM') echo 'selected'; ?>>BSHM</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
        </div>
        <div class="form-group">
            <label for="address">Home Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $address; ?>">
        </div>
        <button type="submit" class="save-btn">Save</button>
    </form>
</div>

<script>
    function triggerFileInput() {
        document.getElementById("profileInput").click();
    }

    document.getElementById("profileInput").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("profilePreview").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>