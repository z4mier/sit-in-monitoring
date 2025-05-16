<?php
session_start();
include '../includes/db-connection.php';

$mode = $_GET['mode'] ?? 'all_time';
$data = [];

$sql = "SELECT s.id_no, u.profile_picture, u.firstname, u.middlename, u.lastname,
               COUNT(*) AS total_sessions, SUM(points) AS total_points
        FROM sit_in_records s
        JOIN users u ON s.id_no = u.id_no
        GROUP BY s.id_no, u.profile_picture, u.firstname, u.middlename, u.lastname";

if ($mode === 'most_active') {
    $sql .= " ORDER BY total_sessions DESC LIMIT 5";
} elseif ($mode === 'top_performers') {
    $sql .= " ORDER BY total_points DESC LIMIT 5";
} else {
    $sql .= " ORDER BY total_points DESC LIMIT 5";
}

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['name'] = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
        $data[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; display: flex; }
    .main-content { margin-left: 80px; padding: 20px; flex: 1; }
    .sidebar:hover ~ .main-content { margin-left: 200px; }
    .tab-buttons { display: flex; justify-content: center; gap: 16px; margin-bottom: 40px; }
    .tab-buttons a { padding: 10px 24px; border-radius: 9999px; font-weight: 600; text-decoration: none; background-color: #1f2937; color: #fff; }
    .tab-buttons a.active { background-color: #2563eb; color: #fff; box-shadow: 0 0 8px rgba(37, 99, 235, 0.6);}
    .podium-container { display: flex; justify-content: center; align-items: end; gap: 40px; margin-bottom: 30px; }
    .podium-avatar { background-size: cover; background-position: center; border-radius: 9999px;}
    .podium-label { text-align: center; margin-top: 10px; }
    .remaining-entry { display: flex; justify-content: space-between; align-items: center; background-color: #1f2937; border-radius: 16px; padding: 12px 20px; margin-bottom: 12px; }
    .leaderboard-box { padding: 20px; background-color: #0d121e; border: 2px solid white; border-radius: 20px; box-shadow: 0 0 10px rgba(255,255,255,0.1); max-width: 900px; margin: auto; }
  </style>
</head>
<body>
<?php include '../includes/admin-sidebar.php'; ?>
<div class="main-content">
  <div class="pb-4 border-b-2 border-white/10 mb-6">
    <h1 class="text-3xl font-bold text-white">Admin - Leaderboard</h1>
  </div>

  <div class="leaderboard-box">
    <div class="tab-buttons">
      <a href="?mode=all_time" class="<?= $mode === 'all_time' ? 'active' : '' ?>">All Time</a>
      <a href="?mode=most_active" class="<?= $mode === 'most_active' ? 'active' : '' ?>">Most Active</a>
      <a href="?mode=top_performers" class="<?= $mode === 'top_performers' ? 'active' : '' ?>">Top Performers</a>
    </div>

    <?php if (count($data) >= 3): ?>
      <div class="podium-container">
        <!-- 2nd -->
        <div class="flex flex-col items-center">
          <div class="podium-avatar w-20 h-20 border-4 border-blue-500"
               style="background-image: url('../<?= !empty($data[1]['profile_picture']) ? htmlspecialchars($data[1]['profile_picture']) : 'uploads/default.png' ?>');"></div>
          <div class="podium-label">
            <p class="text-white font-bold text-md"><?= htmlspecialchars($data[1]['name']) ?></p>
            <?php if ($mode === 'most_active'): ?>
              <p class="text-blue-400 text-sm font-bold"><?= $data[1]['total_sessions'] ?> sessions</p>
            <?php elseif ($mode === 'all_time'): ?>
              <p class="text-blue-400 text-sm font-bold"><?= $data[1]['total_points'] ?> pts • <?= $data[1]['total_sessions'] ?> sessions</p>
            <?php else: ?>
              <p class="text-blue-400 text-sm font-bold"><?= $data[1]['total_points'] ?> pts</p>
            <?php endif; ?>
            <p class="text-xs text-gray-400 mt-1">Rank 2</p>
          </div>
        </div>

        <!-- 1st -->
        <div class="relative flex flex-col items-center">
          <img src="../uploads/crown.png" class="w-8 absolute -top-5" />
          <div class="podium-avatar w-24 h-24 border-4 border-yellow-400"
               style="background-image: url('../<?= !empty($data[0]['profile_picture']) ? htmlspecialchars($data[0]['profile_picture']) : 'uploads/default.png' ?>');"></div>
          <div class="podium-label">
            <p class="text-yellow-400 font-bold text-lg"><?= htmlspecialchars($data[0]['name']) ?></p>
            <?php if ($mode === 'most_active'): ?>
              <p class="text-yellow-300 text-sm font-bold"><?= $data[0]['total_sessions'] ?> sessions</p>
            <?php elseif ($mode === 'all_time'): ?>
              <p class="text-yellow-300 text-sm font-bold"><?= $data[0]['total_points'] ?> pts • <?= $data[0]['total_sessions'] ?> sessions</p>
            <?php else: ?>
              <p class="text-yellow-300 text-sm font-bold"><?= $data[0]['total_points'] ?> pts</p>
            <?php endif; ?>
            <p class="text-xs text-gray-400 mt-1">Rank 1</p>
          </div>
        </div>

        <!-- 3rd -->
        <div class="flex flex-col items-center">
          <div class="podium-avatar w-20 h-20 border-4 border-green-500"
               style="background-image: url('../<?= !empty($data[2]['profile_picture']) ? htmlspecialchars($data[2]['profile_picture']) : 'uploads/default.png' ?>');"></div>
          <div class="podium-label">
            <p class="text-white font-bold text-md"><?= htmlspecialchars($data[2]['name']) ?></p>
            <?php if ($mode === 'most_active'): ?>
              <p class="text-green-400 text-sm font-bold"><?= $data[2]['total_sessions'] ?> sessions</p>
            <?php elseif ($mode === 'all_time'): ?>
              <p class="text-green-400 text-sm font-bold"><?= $data[2]['total_points'] ?> pts • <?= $data[2]['total_sessions'] ?> sessions</p>
            <?php else: ?>
              <p class="text-green-400 text-sm font-bold"><?= $data[2]['total_points'] ?> pts</p>
            <?php endif; ?>
            <p class="text-xs text-gray-400 mt-1">Rank 3</p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Remaining -->
    <div class="w-full max-w-md mx-auto">
      <?php for ($i = 3; $i < count($data); $i++): ?>
        <div class="remaining-entry">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-cover bg-center"
                 style="background-image: url('../<?= !empty($data[$i]['profile_picture']) ? htmlspecialchars($data[$i]['profile_picture']) : 'assets/icon.png' ?>');"></div>
            <div>
              <p class="font-semibold text-white"><?= htmlspecialchars($data[$i]['name']) ?></p>
              <p class="text-gray-400 text-sm">Rank <?= $i + 1 ?></p>
            </div>
          </div>
          <?php if ($mode === 'most_active'): ?>
            <p class="font-bold text-lg text-white"><?= $data[$i]['total_sessions'] ?> sessions</p>
          <?php elseif ($mode === 'all_time'): ?>
            <p class="font-bold text-lg text-white"><?= $data[$i]['total_points'] ?> pts • <?= $data[$i]['total_sessions'] ?> sessions</p>
          <?php else: ?>
            <p class="font-bold text-lg text-white"><?= $data[$i]['total_points'] ?> pts</p>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</div>
</body>
</html>
