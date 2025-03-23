<?php
session_start();

$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        echo "<pre>";
        print_r($user);
        echo "</pre>";

        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'username' => $user['username'],
                'role' => $user['role']
            ];

            if ($user['role'] === 'admin') {
                header("Location: admin/admin-home.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            echo "Password does not match.";
        }
    } else {
        echo "User not found in database.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring System</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
    body {
        font-family: 'Inter', sans-serif;
            background-image: url('assets/bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #f7f7f7;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #144d94;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 15px;
        }
        input[type="submit"]:hover {
            background-color: #113d74;
        }
    </style>
</head>
<body>
    <div id="frm" style="width: 400px; margin: 100px auto; padding: 40px; background-color: rgba(255, 255, 255, 0.6); box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 10px;">
        <div class="logo" style="text-align: center; display: flex; justify-content: center; align-items: center; gap: 30px;">
            <img src="assets/uc.png" alt="School Logo" style="width: 130px; height: auto;">
            <img src="assets/ccs.png" alt="CCS Logo" style="width: 100px; height: auto;">
        </div>
        <h1 style="text-align: center; font-size: 25px; margin-bottom: 20px;">CCS Sit-In Monitoring System</h1>
        <?php if (isset($error)) echo "<p style='color: red; text-align: center;'>$error</p>"; ?>
        <form method="POST" action="">
            <div style="margin-bottom: 20px;">
                <label for="username" style="display: block; font-weight: bold; margin-bottom: 5px;">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; margin-bottom: 5px;">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div style="text-align: center;">
                <input type="submit" value="Login">
            </div>
        </form>
        <div style="text-align: center; margin-top: 20px; font-size: 15px;">
            <p>Don't have an account? <a href="register.php" style="color: #144d94;">Register here</a></p>
        </div>
    </div>
</body>
</html>
