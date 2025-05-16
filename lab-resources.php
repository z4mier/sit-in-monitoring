<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include 'includes/db-connection.php';
$user = $_SESSION['user'];

$resources = [];
$res = $conn->query("SELECT * FROM lab_resources ORDER BY created_at DESC");
if ($res && $res->num_rows > 0) {
    $resources = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lab Resources</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background-color: #0d121e;
      color: white;
      margin: 0;
      padding: 0;
    }

    .content {
      margin-left: 270px;
      padding: 30px;
      box-sizing: border-box;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .resource-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }

    .resource-card {
      background-color: #181a25;
      border-radius: 15px;
      padding: 15px;
      border: 1px solid #333;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .resource-card img {
      width: 100%;
      height: 130px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    .resource-card h4 {
      margin: 0 0 8px;
      font-size: 16px;
    }

    .resource-card a {
      color: #3b82f6;
      font-size: 14px;
      text-decoration: none;
      margin-top: auto;
    }

    .resource-card a:hover {
      text-decoration: underline;
    }

    .no-resources {
      text-align: center;
      color: #aaa;
      font-size: 16px;
      margin-top: 60px;
    }

    @media screen and (max-width: 1024px) {
      .content {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="content">
  <h1><i class="fas fa-book-open"></i> Lab Resources</h1>

  <?php if (!empty($resources)): ?>
    <div class="resource-grid">
      <?php foreach ($resources as $res): ?>
        <div class="resource-card">
          <img src="uploads/<?= htmlspecialchars($res['image_path']) ?>" alt="cover">
          <h4><?= htmlspecialchars($res['title']) ?></h4>
          <a href="<?= htmlspecialchars($res['link']) ?>" target="_blank">Visit Resource</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="no-resources">No resources available yet.</div>
  <?php endif; ?>
</div>

</body>
</html>
