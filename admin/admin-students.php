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
    <title>Admin Students</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            background-color: #0d121e;
            color: #ffffff;
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
            padding: 20px;
            border-bottom: 2px solid #333;
        }

        header h1 {
            margin: 0;
            color: #ffffff;
        }

        .table-container {
            margin-top: 20px;
            border-radius: 10px;
            padding: 20px;   
        }

        .table-container h2 {
            margin: 0 0 20px 0;
            color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
        }

        table th {
            background-color: #0d121e;
            
        }

        table tr:nth-child(even) {
            background-color: #111524;
        }

        table tr:nth-child(odd) {
            background-color: #212b40;
        }

        table tr:hover {
            background-color: #181a25;
        }

        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .search-container input {
            padding: 10px 10px 10px 30px; /* Add padding to the left for the icon */
            border: none;
            border-radius: 5px;
            width: 200px;
            background-color: #333;
            color: #fff;
        }

        .search-container .search-icon {
            position: absolute;
            left: 10px;
            color: #fff;
        }

        .add-button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #ffffff;
            padding: 10px 15px;
            text-decoration: none;
            border: 1px solid #333;
            margin: 0 5px;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #3498db;
            border-color: #3498db;
        }

        .pagination a:hover {
            background-color: #3a3a4c;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 500px;
            border-radius: 10px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .modal-header h2 {
            margin: 0;
        }

        .modal-body {
            margin-top: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-close {
            background-color: #ccc;
            color: #000;
        }

        .btn-sit-in {
            background-color: #3498db;
            color: #fff;
            margin-left: 10px;
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
            <h1>Students</h1>
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search contacts">
                <button class="add-button" id="openModalBtn"><i class="fas fa-plus"></i></button>
            </div>
        </header>

        <!-- Table Section -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>LOCATION</th>
                        <th>PHONE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="../assets/profile1.jpg" alt="Profile Image" class="profile-image"> Felicia Burke</td>
                        <td>example@mail.com</td>
                        <td>Hong Kong, China</td>
                        <td>+1 (070) 123-4567</td>
                    </tr>
                    <tr>
                        <td><img src="../assets/profile2.jpg" alt="Profile Image" class="profile-image"> Pamela Garza</td>
                        <td>example@mail.com</td>
                        <td>Boston, USA</td>
                        <td>+1 (070) 123-4567</td>
                    </tr>
                    <tr>
                        <td><img src="../assets/profile3.jpg" alt="Profile Image" class="profile-image"> Sophia Hale</td>
                        <td>example@mail.com</td>
                        <td>New York, USA</td>
                        <td>+1 (070) 123-4567</td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <a href="#">&laquo;</a>
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">4</a>
                <a href="#">5</a>
                <a href="#">&raquo;</a>
            </div>
        </div>
    </div>

    <!-- Include Modal -->
    <?php include '../includes/sit-in-form.php'; ?>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "../includes/logout.php";
            }
        }
    </script>
</body>
</html>