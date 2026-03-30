<?php
session_start();
require_once '../config.php';

// Fetch admin profile
$admin_query = mysqli_query($con, "SELECT * FROM user WHERE role='admin' LIMIT 1");
$admin = mysqli_fetch_assoc($admin_query);

if (!$admin) {
    // Fallback if no admin exists
    $admin = [
        'id' => 0,
        'fullname' => 'Super Admin',
        'email' => 'admin@filmflix.com',
        'number' => '9999999999',
        'city' => 'Mumbai',
        'role' => 'admin',
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s'),
        'image' => null
    ];
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image'])) {
    if (!empty($_FILES['image']['name'])) {
        $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['image']['name']);
        if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img)) {
            $id = $admin['id'];
            if ($id > 0) {
                mysqli_query($con, "UPDATE user SET image='$img' WHERE id=$id");
                $admin['image'] = $img;
            }
            $msg = ['type'=>'success','text'=>'Profile picture updated successfully.'];
        } else {
            $msg = ['type'=>'error','text'=>'Failed to upload image.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
<style>
.profile-cover {
  height: 200px;
  background: linear-gradient(135deg, rgba(168,85,247,0.4), rgba(6,182,212,0.4)), url('../assets/images/banner_bg.jpg') center/cover;
  border-radius: var(--radius) var(--radius) 0 0;
  position: relative;
}
.profile-header {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-top: none;
  border-radius: 0 0 var(--radius) var(--radius);
  padding: 0 32px 32px;
  margin-bottom: 24px;
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}
.profile-avatar-wrap {
  position: relative;
  margin-top: -60px;
  display: flex;
  align-items: flex-end;
  gap: 20px;
}
.profile-avatar {
  width: 140px; height: 140px;
  border-radius: 50%;
  border: 4px solid var(--bg-primary);
  background: #1a1a28;
  display: flex; align-items: center; justify-content: center;
  font-size: 50px; font-weight: 700; color: #fff;
  overflow: hidden;
  box-shadow: var(--shadow);
}
.profile-avatar img {
  width: 100%; height: 100%; object-fit: cover;
}
.profile-info h1 {
  font-size: 24px; font-weight: 800; margin-bottom: 4px; color: var(--text-primary);
}
.profile-info p {
  font-size: 13.5px; color: var(--accent-cyan); font-weight: 500;
}
.camera-btn {
  position: absolute;
  bottom: 5px; right: 5px;
  width: 36px; height: 36px;
  border-radius: 50%;
  background: var(--bg-primary);
  border: 1px solid var(--border);
  color: var(--text-primary);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: var(--transition);
}
.camera-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.detail-item {
  background: rgba(255,255,255,0.02);
  padding: 16px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
}
.detail-label {
  font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;
}
.detail-value {
  font-size: 14px; font-weight: 500; color: var(--text-primary);
}
</style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
  <?php include 'includes/topbar.php'; ?>
  <div class="page-content">

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="profile-container">
      <div class="profile-cover"></div>
      <div class="profile-header">
        <div class="profile-avatar-wrap">
          <div style="position:relative;">
            <div class="profile-avatar">
              <?php if (!empty($admin['image'])): ?>
                <img src="../<?= htmlspecialchars($admin['image']) ?>" alt="Avatar">
              <?php else: ?>
                <?= strtoupper(substr($admin['fullname'], 0, 1)) ?>
              <?php endif; ?>
            </div>
            
            <form method="POST" enctype="multipart/form-data" id="avatarForm">
               <input type="hidden" name="update_image" value="1">
               <input type="file" name="image" id="avatarInput" style="display:none;" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
               <button type="button" class="camera-btn" onclick="document.getElementById('avatarInput').click()" title="Update Picture">
                 <i class="fas fa-camera"></i>
               </button>
            </form>
          </div>
          <div class="profile-info" style="margin-bottom:15px;">
            <h1><?= htmlspecialchars($admin['fullname']) ?></h1>
            <p><i class="fas fa-user-shield" style="margin-right:5px;"></i>System Administrator</p>
          </div>
        </div>
        <div style="margin-bottom:15px;">
          <a href="settings.php" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Profile</a>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;">
        
        <!-- Main Details -->
        <div class="content-card" style="margin-bottom:0;">
          <h3 class="card-title"><i class="fas fa-address-card"></i> Administrator Details</h3>
          <div class="details-grid">
            <div class="detail-item">
              <div class="detail-label">Full Name</div>
              <div class="detail-value"><?= htmlspecialchars($admin['fullname']) ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Email Address</div>
              <div class="detail-value"><?= htmlspecialchars($admin['email']) ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Phone Number</div>
              <div class="detail-value"><?= htmlspecialchars($admin['number']) ?: 'Not Provided' ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Location Base</div>
              <div class="detail-value"><?= htmlspecialchars($admin['city']) ?: 'Not Provided' ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Account Status</div>
              <div class="detail-value">
                 <span class="status-badge active">Active</span>
              </div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Joined Platform</div>
              <div class="detail-value"><?= date('F j, Y', strtotime($admin['created_at'])) ?></div>
            </div>
          </div>
        </div>

        <!-- System Stats Mini -->
        <div style="display:flex;flex-direction:column;gap:24px;">
           <div class="content-card" style="margin-bottom:0;">
             <h3 class="card-title"><i class="fas fa-shield-alt"></i> Security Status</h3>
             <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:16px;">
               <li style="display:flex;align-items:center;gap:12px;">
                 <div style="width:32px;height:32px;border-radius:50%;background:rgba(16,185,129,0.1);color:#10b981;display:flex;align-items:center;justify-content:center;"><i class="fas fa-check"></i></div>
                 <div>
                   <div style="font-size:13px;font-weight:600;color:var(--text-primary);">Account Secured</div>
                   <div style="font-size:11px;color:var(--text-muted);">2-Factor Authentication inactive</div>
                 </div>
               </li>
               <li style="display:flex;align-items:center;gap:12px;">
                 <div style="width:32px;height:32px;border-radius:50%;background:rgba(16,185,129,0.1);color:#10b981;display:flex;align-items:center;justify-content:center;"><i class="fas fa-check"></i></div>
                 <div>
                   <div style="font-size:13px;font-weight:600;color:var(--text-primary);">Email Verified</div>
                   <div style="font-size:11px;color:var(--text-muted);">Verified at registration</div>
                 </div>
               </li>
               <li style="display:flex;align-items:center;gap:12px;">
                 <div style="width:32px;height:32px;border-radius:50%;background:rgba(239,68,68,0.1);color:#ef4444;display:flex;align-items:center;justify-content:center;"><i class="fas fa-exclamation"></i></div>
                 <div>
                   <div style="font-size:13px;font-weight:600;color:var(--text-primary);">Last Password Change</div>
                   <div style="font-size:11px;color:var(--text-muted);">Over 90 days ago</div>
                 </div>
               </li>
             </ul>
           </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
<script src="assets/admin.js"></script>
</body>
</html>
