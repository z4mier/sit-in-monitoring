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
    <title>Admin Announcements</title>
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
            margin-left: 80px;
            padding: 20px;
            width: calc(100% - 80px);
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 200px;
            width: calc(100% - 200px);
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #333;
        }
        .content-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-top: 20px;
        }
        .box {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            min-height: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .box-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            border-bottom: 2px solid white;
            padding-bottom: 10px;
        }
        .announcement-form textarea {
            width: 96%;
            height: 200px;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #212b40;
            color: white;
            resize: none;
        }

        .form-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .announcement-form button {
            padding: 10px 20px;
            background-color: white;
            color: #0d121e;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
        }
        .announcements-container {
            max-height: 350px;
            overflow-y: auto;
            padding: 20px;
            border-radius: 10px;
        }
        .announcement-item {
            position: relative;
            background-color: #212b40;
            padding: 15px;
            border-radius: 2px;
            margin-bottom: 15px;
        }
        .announcement-item small {
            display: block;
            margin-top: 5px;
            color: #aaa;
            font-size: 12px;
        }

    </style>
</head>
<body>
        <!-- Sidebar -->
        <?php include '../includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Announcements</h1>
        </header>

        <div class="content-wrapper">
            <!-- Create Announcement Box -->
            <div class="box">
                <div class="box-title">Create Announcement</div>
                <form method="POST" action="../includes/add-announcement.php" class="announcement-form">
                    <textarea name="announcement_text" placeholder="Write your announcement here..." required></textarea>
                    <div class="form-footer">
                        <button type="submit">Publish</button>
                    </div>
                </form>
            </div>

            <!-- Published Announcements Box -->
            <div class="box announcements-container">
                <div class="box-title">Published Announcements</div>
                <?php include '../includes/fetch-announcement.php'; ?>
            </div>
        </div>
    </div>
    <script>
function editAnnouncement(id) {
    document.getElementById("announcement-text-" + id).style.display = "none";
    document.getElementById("edit-form-" + id).style.display = "block";
}

function cancelEdit(id) {
    document.getElementById("announcement-text-" + id).style.display = "block";
    document.getElementById("edit-form-" + id).style.display = "none";
}

function deleteAnnouncement(id) {
    if (confirm("Are you sure you want to delete this announcement?")) {
        let form = document.createElement("form");
        form.method = "POST";
        form.action = "../includes/delete-announcement.php";

        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "announcement_id";
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>
