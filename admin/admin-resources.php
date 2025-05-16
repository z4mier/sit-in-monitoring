<?php
session_start();
include '../includes/db-connection.php';

$notif = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $title = $_POST['title'] ?? '';
    $link = $_POST['link'] ?? '';
    $image = $_FILES['image'] ?? null;

    if ($title && $link && $image && $image['tmp_name']) {
        $upload_dir = '../uploads/';
        $filename = uniqid() . '-' . basename($image['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO lab_resources (title, image_path, link) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $filename, $link);
            if ($stmt->execute()) {
                $notif = "Resource uploaded successfully.";
            } else {
                $notif = "Failed to save to database.";
            }
        } else {
            $notif = "Failed to upload image.";
        }
    } else {
        $notif = "All fields are required.";
    }
}

$resources = [];
$res = $conn->query("SELECT * FROM lab_resources ORDER BY created_at DESC");
if ($res && $res->num_rows > 0) {
    $resources = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Lab Resources</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0d121e; color: white; display: flex; }
    .main-content { margin-left: 80px; padding: 20px; flex: 1; }
    .sidebar:hover ~ .main-content { margin-left: 200px; width: calc(100% - 200px); }

    h1 { font-size: 28px; margin-bottom: 20px; }

    .dashboard-row { display: flex; gap: 20px; flex-wrap: wrap; }
    .form-box, .resource-box {
      flex: 1;
      background-color: #0d121e;
      border: 2px solid white;
      border-radius: 20px;
      padding: 25px;
      max-height: 700px;
      overflow-y: auto;
    }

    .form-box h3, .resource-box h3 { text-align: center; font-size: 20px; margin-bottom: 20px; }
    .upload-form label { display: block; margin-bottom: 6px; margin-top: 10px; }
    .upload-form input[type="text"],
    .upload-form input[type="url"],
    .upload-form input[type="file"] {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: none;
    }
    .upload-form button {
      margin-top: 15px;
      padding: 10px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 10px;
      width: 100%;
      cursor: pointer;
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
    }

    .resource-card img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 10px;
    }

    .resource-card h4 { margin: 10px 0 5px; font-size: 16px; }
    .resource-card a { color: #3b82f6; font-size: 14px; word-break: break-word; }

    /* Edit Modal */
    #editModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
      z-index: 10000;
    }

    #editForm {
      background: #181a25;
      padding: 30px;
      border-radius: 15px;
      width: 400px;
      color: white;
      position: relative;
    }

    #editForm input[type="text"],
    #editForm input[type="url"],
    #editForm input[type="file"] {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: none;
      margin-bottom: 10px;
    }

    #editForm button[type="submit"] {
      width: 100%;
      padding: 10px;
      background: #3b82f6;
      border: none;
      border-radius: 10px;
      color: white;
    }

    #editForm .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      background: none;
      border: none;
      color: white;
      font-size: 20px;
    }
  </style>
</head>
<body>
<?php include '../includes/admin-sidebar.php'; ?>
<div class="main-content">
  <h1>Admin - Lab Resources</h1>

  <div class="dashboard-row">
    <!-- Upload Form Container -->
    <div class="form-box">
      <h3><i class="fas fa-upload"></i> Upload New Resource</h3>
      <form method="POST" enctype="multipart/form-data" class="upload-form">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Link:</label>
        <input type="url" name="link" required>

        <label>Cover Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit"><i class="fas fa-paper-plane"></i> Submit</button>
      </form>
    </div>

    <!-- Resource List Container -->
    <div class="resource-box">
      <h3><i class="fas fa-book-reader"></i> Uploaded Resources</h3>
      <?php if (!empty($resources)): ?>
        <div class="resource-grid">
          <?php foreach ($resources as $res): ?>
            <div class="resource-card" data-id="<?= $res['id'] ?>">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <h4><?= htmlspecialchars($res['title']) ?></h4>
                <div>
                  <a href="#" class="edit-resource" title="Edit"
                     data-title="<?= htmlspecialchars($res['title']) ?>"
                     data-link="<?= htmlspecialchars($res['link']) ?>"
                     data-id="<?= $res['id'] ?>">
                    <i class="fas fa-edit" style="color:#60a5fa; margin-right:10px;"></i>
                  </a>
                  <a href="#" class="delete-resource" title="Delete">
                    <i class="fas fa-trash-alt" style="color:#f87171;"></i>
                  </a>
                </div>
              </div>
              <img src="../uploads/<?= htmlspecialchars($res['image_path']) ?>" alt="cover">
              <a href="<?= htmlspecialchars($res['link']) ?>" target="_blank">Visit</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="text-align:center; color: #999;">No resources uploaded yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ✅ Edit Modal -->
<div id="editModal">
  <form id="editForm" method="POST" enctype="multipart/form-data">
    <button type="button" class="close-btn" onclick="document.getElementById('editModal').style.display='none'">&times;</button>
    <h3 style="text-align:center;">Edit Resource</h3>
    <input type="hidden" name="id" id="edit-id">
    <label>Title:</label>
    <input type="text" name="title" id="edit-title" required>
    <label>Link:</label>
    <input type="url" name="link" id="edit-link" required>
    <label>Replace Image (optional):</label>
    <input type="file" name="image">
    <button type="submit">Update</button>
  </form>
</div>

<script>
// Delete
document.querySelectorAll('.delete-resource').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const card = this.closest('.resource-card');
    const id = card.getAttribute('data-id');
    if (confirm('Are you sure you want to delete this resource?')) {
      fetch('../includes/delete-resource.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'delete_id=' + encodeURIComponent(id)
      })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'success') {
          card.remove();
        } else {
          alert('Failed to delete. Try again.');
        }
      });
    }
  });
});

// Open Edit Modal
document.querySelectorAll('.edit-resource').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('edit-id').value = this.dataset.id;
    document.getElementById('edit-title').value = this.dataset.title;
    document.getElementById('edit-link').value = this.dataset.link;
    document.getElementById('editModal').style.display = 'flex';
  });
});

// Handle Edit Submit
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('../includes/update-resource.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    if (response.trim() === 'success') {
      location.reload();
    } else {
      alert('Update failed.');
    }
  });
});
</script>
</body>
</html>
