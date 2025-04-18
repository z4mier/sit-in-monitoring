<?php
session_start();
include '../includes/db-connection.php';

$feedback_rows = '';

// Get feedback with corresponding sit-in info
$query = "
SELECT 
    f.id_no, 
    u.firstname, 
    u.lastname, 
    COALESCE(s.lab_number, 'N/A') AS lab_number, 
    COALESCE(s.purpose, 'N/A') AS purpose, 
    f.message, 
    f.date
FROM feedback f
LEFT JOIN users u ON f.id_no = u.id_no
LEFT JOIN (
    SELECT id_no, lab_number, purpose, date
    FROM sit_in_records s
    WHERE (id_no, date, id) IN (
        SELECT id_no, date, MAX(id)
        FROM sit_in_records
        GROUP BY id_no, date
    )
) s ON f.id_no = s.id_no AND f.date = s.date
ORDER BY f.date DESC
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $full_name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
        $feedback_rows .= "<tr>
            <td>" . htmlspecialchars($row['id_no']) . "</td>
            <td>$full_name</td>
            <td>" . htmlspecialchars($row['lab_number']) . "</td>
            <td>" . htmlspecialchars($row['purpose']) . "</td>
            <td>" . htmlspecialchars($row['date']) . "</td>
            <td>" . nl2br(htmlspecialchars($row['message'])) . "</td>
        </tr>";
    }
} else {
    $feedback_rows = "<tr><td colspan='6' style='text-align: center;'>No feedback records found.</td></tr>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Feedback Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #0d121e;
      color: #ffffff;
      display: flex;
    }
    .main-content {
      margin-left: 80px;
      padding: 20px;
      flex: 1;
    }
    .sidebar:hover ~ .main-content {
      margin-left: 180px;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 2px solid #333;
    }
    h1 {
      margin: 0;
      font-size: 28px;
    }
    .table-container {
      margin-top: 20px;
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th, table td {
      padding: 15px;
      text-align: center;
    }
    thead tr {
      background-color: transparent !important;
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
  </style>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main-content">
  <header>
    <h1>Feedback Reports</h1>
  </header>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID Number</th>
          <th>Name</th>
          <th>Lab #</th>
          <th>Purpose</th>
          <th>Date</th>
          <th>Message</th>
        </tr>
      </thead>
      <tbody>
        <?= $feedback_rows ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
