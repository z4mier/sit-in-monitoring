<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$conn = new mysqli("localhost", "root", "", "sysarch");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CCS Sit-In Monitoring System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
            body { font-family: 'Inter', sans-serif; background-color: #0d121e; margin: 0; padding: 0; color: white; }
            .header { padding: 20px; text-align: center; }
            .header img { width: 60px; height: auto; vertical-align: middle; margin-right: 12px; }
            .header h1 { display: inline-block; vertical-align: middle; font-size: 30px; font-weight: 700; margin: 0; }
            .content { margin-left: 270px; padding: 20px 10px; box-sizing: border-box; }
            .dashboard-row { display: flex; gap: 20px; flex-wrap: wrap; }
            .dashboard-row > .announcements, .dashboard-row > .right-column { flex: 1; min-width: 0; }
            .announcements { display: flex; flex-direction: column; gap: 20px; padding: 20px; border-radius: 15px; border: 2px solid white; background-color: #0d121e; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); max-height: 600px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #555 transparent; }
            .widget-box { padding: 20px; border: 2px solid white; border-radius: 15px; max-height: 600px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #555 transparent; }
            .announcements::-webkit-scrollbar, .widget-box::-webkit-scrollbar { width: 8px; }
            .announcements::-webkit-scrollbar-track, .widget-box::-webkit-scrollbar-track { background: transparent; }
            .announcements::-webkit-scrollbar-thumb, .widget-box::-webkit-scrollbar-thumb { background-color: #555; border-radius: 10px; border: 2px solid transparent; background-clip: content-box; }
            .announcements::-webkit-scrollbar-thumb:hover, .widget-box::-webkit-scrollbar-thumb:hover { background-color: #777; }
            .announcements h3, .widget-box h3 { font-weight: bold; font-size: 20px; text-align: center; }
            .card { background-color: transparent; border: 1px solid white; padding: 18px; border-radius: 10px; }
            .card p { margin: 8px 0 0; font-size: 14px; }
            .date-posted { font-size: 14px; color: #ccc; margin-bottom: 6px; }
            .right-column { display: flex; flex-direction: column; gap: 20px; }
            .input-group { display: flex; gap: 10px; margin-bottom: 15px; }
            #todo-input { flex: 1; padding: 10px; border-radius: 8px; border: none; outline: none; }
            #todo-form button { background-color: #4caf50; color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; }
            #todo-form button:hover { background-color: #45a049; }
            .todo-list { list-style: none; padding: 0; margin: 0; }
            .todo-list li { background-color: transparent; border: 1px solid white; border-radius: 10px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; transition: 0.2s; }
            .todo-list li:hover { background-color: rgba(255, 255, 255, 0.05); }
            .todo-left { display: flex; align-items: center; gap: 10px; }
            .todo-left span { font-size: 15px; transition: 0.3s; }
            .todo-left span.completed { text-decoration: line-through; opacity: 0.6; }
            .todo-actions button { background: none; border: none; color: #ccc; cursor: pointer; font-size: 16px; margin-left: 8px; }
            .todo-actions button:hover { color: white; }
            @media screen and (max-width: 768px) { .announcements { width: 100%; } .content { margin-left: 0; } .dashboard-row { flex-direction: column; } }
    </style>

</head>
<body>
    <div class="header">
        <img src="assets/ccs.png" alt="CCS Logo">
        <h1>CCS Sit-In Monitoring System</h1>
    </div>

    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="dashboard-row">

            <div class="announcements">
                <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
                <?php
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
                ?>
            </div>

            <div class="right-column">
                <div class="widget-box">
                    <h3><i class="fas fa-check-square"></i> To-Do / Reminders</h3>
                    <form id="todo-form" onsubmit="addTask(event)">
                        <div class="input-group">
                            <input type="text" id="todo-input" placeholder="Add a task..." required>
                            <button type="submit">Add</button>
                        </div>
                    </form>
                    <ul class="todo-list" id="todo-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addTask(event) {
            event.preventDefault();
            const input = document.getElementById('todo-input');
            const value = input.value.trim();
            if (!value) return;

            const li = document.createElement('li');
            li.innerHTML = `
                <div class="todo-left">
                    <input type="checkbox" onchange="toggleTask(this)">
                    <span>${value}</span>
                </div>
                <div class="todo-actions">
                    <button onclick="editTask(this)"><i class="fas fa-edit"></i></button>
                    <button onclick="deleteTask(this)"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            document.getElementById('todo-list').appendChild(li);
            input.value = '';
        }

        function toggleTask(checkbox) {
            const span = checkbox.nextElementSibling;
            span.classList.toggle('completed', checkbox.checked);
        }

        function deleteTask(btn) {
            const li = btn.closest('li');
            li.remove();
        }

        function editTask(btn) {
            const li = btn.closest('li');
            const span = li.querySelector('span');
            const current = span.textContent;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = current;
            input.style.flex = '1';
            input.style.marginLeft = '8px';

            input.addEventListener('blur', () => {
                span.textContent = input.value.trim() || current;
                span.style.display = 'inline';
                input.remove();
            });

            span.style.display = 'none';
            span.parentElement.appendChild(input);
            input.focus();
        }
    </script>
</body>
</html>
