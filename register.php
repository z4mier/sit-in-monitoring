<?php
session_start();
    
$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_no = trim($_POST['id_no']);
    $lastname = trim($_POST['lastname']);
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $yr_level = trim($_POST['yr_level']);
    $course = trim($_POST['course']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    if (!empty($id_no) && !empty($lastname) && !empty($firstname) && !empty($yr_level) && !empty($course) && !empty($username) && !empty($_POST['password'])) {
        // Check if username already exists
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists. Please choose a different one.";
        } else {
            // Insert new user
            $sql = "INSERT INTO users (id_no, lastname, firstname, middlename, yr_level, course, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $id_no, $lastname, $firstname, $middlename, $yr_level, $course, $username, $password);

            if ($stmt->execute()) {
                header("Location: index.php?registered=true");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    } else {
        $error = "Please fill out all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CCS Sit-In Monitoring System</title>
    <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
            body { font-family: 'Inter', sans-serif; background-image: url('assets/bg.jpg'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed; margin: 0; padding: 0; }
            #frm { width: 500px; margin: 100px auto; padding: 40px; background-color: rgba(255, 255, 255, 0.6); box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 10px; }
            h1 { text-align: center; font-size: 24px; margin-bottom: 20px; }
            label { display: block; margin-bottom: 5px; font-weight: bold; }
            input[type="text"], input[type="password"], select { width: 100%; padding: 12px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 25px; box-sizing: border-box; font-size: 16px; background-color: #f7f7f7; }
            input[type="submit"] { width: 100%; background-color: #144d94; color: white; padding: 10px; border: none; border-radius: 25px; cursor: pointer; font-size: 15px; }
            input[type="submit"]:hover { background-color: #113d74; }
            p { text-align: center; font-size: 15px; }
            a { color: #144d94; text-decoration: none; }
    </style>

</head>
<body>
    <div id="frm">
        <h1>Register</h1>
        <?php if (isset($error)) echo "<p style='color: red; text-align: center;'>$error</p>"; ?>
        <form method="POST" action="">
            <label>ID No:</label>
            <input type="text" name="id_no" required>
            <label>Last Name:</label>
            <input type="text" name="lastname" required>
            <label>First Name:</label>
            <input type="text" name="firstname" required>
            <label>Middle Name:</label>
            <input type="text" name="middlename">
            <label>Year Level:</label>
            <select name="yr_level" required>
                <option value="" disabled selected>Select Year Level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <label>Course:</label>
            <select name="course" required>
                <option value="" disabled selected>Select Course</option>
                <option value="bsit">BSIT</option>
                <option value="bscs">BSCS</option>
                <option value="bscpe">BSCpE</option>
                <option value="bsed">BSED</option>
                <option value="bshm">BSHM</option>
            </select>
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>
</body>
</html>
