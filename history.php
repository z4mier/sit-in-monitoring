<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$firstname = htmlspecialchars($user['firstname']);
$middlename = isset($user['middlename']) ? htmlspecialchars($user['middlename']) : '';
$lastname = htmlspecialchars($user['lastname']);
$fullname = trim("$firstname $middlename $lastname");
$username = htmlspecialchars($user['username']);

$conn = new mysqli("localhost", "root", "", "sysarch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_no FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_row = $result->fetch_assoc();
    $id_no = $user_row['id_no'];
} else {
    die("Invalid user.");
}

$history_query = "SELECT * FROM sit_in_records WHERE id_no = ? ORDER BY id DESC";
$history_stmt = $conn->prepare($history_query);
$history_stmt->bind_param("s", $id_no);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sit-In History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/04f888fcdb.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0d121e;
            margin: 0;
            padding: 0;
            color: white;
        }
        .container {
            margin-left: 270px;
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
        }
        th {
            background-color: #181a25;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #1c2233;
        }
        tr:nth-child(odd) {
            background-color: #11141f;
        }
        .btn-icon {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-icon:hover {
            color: #ccc;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 99;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #1c2233;
            padding: 20px;
            border-radius: 12px;
            width: 500px;
            color: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
        }
        .modal-content textarea {
            width: 93%;
            height: 120px;
            padding: 12px 15px;
            border-radius: 10px;
            border: none;
            resize: none;
            font-family: 'Inter', sans-serif;
            background-color: #0d121e;
            color: white;
            font-size: 14px;
            margin-top: 10px;
        }
        .modal-content button {
            margin-top: 15px;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            background-color: white;
            color: #0d121e;
            cursor: pointer;
            font-size: 14px;
        }
        .modal-content button:hover {
            background-color: #ccc;
        }
        .modal-content .close {
            float: right;
            font-size: 18px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }
        .modal-content .close:hover {
            color: #ccc;
        }
        .modal-content h3 {
            margin: 0 0 10px;
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="container">
    <h2>Sit-In History</h2>

    <table>
        <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Lab #</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($history_result->num_rows > 0): ?>
            <?php while ($row = $history_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                    <td><?php echo htmlspecialchars($row['lab_number']); ?></td>
                    <td><?php echo date("H:i:s", strtotime($row['time_in'])); ?></td>
                    <td><?php echo date("H:i:s", strtotime($row['time_out'])); ?></td>
                    <td><?php echo date("Y-m-d", strtotime($row['time_in'])); ?></td>
                    <td>
                        <button class="btn-icon" onclick="openFeedbackModal(<?php echo $row['id']; ?>)">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align: center;">No sit-in records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackModal()">&times;</span>
        <h3>Submit Feedback</h3>
        <form method="POST" action="submit_feedback.php">
            <input type="hidden" id="feedback_record_id" name="record_id">
            <textarea name="feedback" placeholder="Enter your feedback here..." required></textarea>
            <div style="text-align: right;">
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
</div>


<script>
    function openFeedbackModal(recordId) {
        document.getElementById("feedback_record_id").value = recordId;
        document.getElementById("feedbackModal").style.display = "flex";
    }

    function closeFeedbackModal() {
        document.getElementById("feedbackModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("feedbackModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>
