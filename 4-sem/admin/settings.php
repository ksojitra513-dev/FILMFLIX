<?php
session_start();
require_once '../config.php';
$msg = '';

// Assuming admin logic: for now we update the primary admin in the DB (role='admin' LIMIT 1)
// In a real system, you'd use $_SESSION['admin_id'] 
$admin_query = mysqli_query($con, "SELECT * FROM user WHERE role='admin' LIMIT 1");
$admin = mysqli_fetch_assoc($admin_query);

if (!$admin) {
    // If no admin exists in the system for testing
    $admin = [
        'id' => 0,
        'fullname' => 'Super Admin',
        'email' => 'admin@filmflix.com',
        'number' => '9999999999',
        'city' => 'Mumbai'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $number = mysqli_real_escape_string($con, $_POST['number']);
        $city = mysqli_real_escape_string($con, $_POST['city']);
        $id = $admin['id'];
        
        if ($id > 0) {
            $sql = "UPDATE user SET fullname='$fullname', email='$email', number='$number', city='$city' WHERE id=$id";
            if (mysqli_query($con, $sql)) {
                $msg = ['type'=>'success','text'=>'Profile updated successfully!'];
                $admin['fullname'] = $fullname;
                $admin['email'] = $email;
                $admin['number'] = $number;
                $admin['city'] = $city;
            } else {
                $msg = ['type'=>'error','text'=>'Error: '.mysqli_error($con)];
            }
        }
    }

    if ($action === 'change_password') {
        $curr = $_POST['current_password'];
        $new = $_POST['new_password'];
        $conf = $_POST['confirm_password'];
        
        if ($new !== $conf) {
            $msg = ['type'=>'error', 'text'=>'New passwords do not match.'];
        } else {
            // Verify current password
            if ($admin['id'] > 0 && password_verify($curr, $admin['password'])) {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                mysqli_query($con, "UPDATE user SET password='$hashed' WHERE id=".$admin['id']);
                $msg = ['type'=>'success', 'text'=>'Password changed successfully!'];
            } else {
                $msg = ['type'=>'error', 'text'=>'Incorrect current password.'];
            }
        }
    }

    if ($action === 'update_system') {
        $title = mysqli_real_escape_string($con, $_POST['site_title']);
        $tagline = mysqli_real_escape_string($con, $_POST['tagline']);
        $email = mysqli_real_escape_string($con, $_POST['contact_email']);
        $social = mysqli_real_escape_string($con, $_POST['social_link']);

        mysqli_query($con, "DELETE FROM site_settings"); // Simple one-row strategy
        $sql = "INSERT INTO site_settings (site_title, tagline, contact_email, social_link) VALUES ('$title', '$tagline', '$email', '$social')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success', 'text'=>'System configuration updated!'];
        else $msg = ['type'=>'error', 'text'=>mysqli_error($con)];
    }
}

// Check for site_settings table
mysqli_query($con, "CREATE TABLE IF NOT EXISTS site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_title VARCHAR(255),
    tagline VARCHAR(255),
    contact_email VARCHAR(255),
    social_link VARCHAR(255)
)");

$site = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM site_settings LIMIT 1")) ?? [
    'site_title' => 'FilmFlix',
    'tagline' => 'Premium Movie Experience',
    'contact_email' => 'support@filmflix.com',
    'social_link' => 'https://facebook.com/filmflix'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Settings</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
  <?php include 'includes/topbar.php'; ?>
  <div class="page-content">
    <div class="page-header">
      <div>
        <h1 class="page-title">Personal Settings</h1>
        <p class="page-subtitle">Manage your admin profile and security</p>
      </div>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:24px;">
      
      <!-- Profile settings -->
      <div class="content-card" style="margin-bottom:0;align-self:start;">
        <h3 class="card-title"><i class="fas fa-user-circle"></i> Profile Information</h3>
        <form method="POST" id="profileForm" novalidate>
          <input type="hidden" name="action" value="update_profile">
          <div class="form-grid cols-1">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
              <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#a855f7,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;">
                <?= strtoupper(substr($admin['fullname'],0,1)) ?>
              </div>
              <div>
                <input type="file" id="avatarUpload" style="display:none;" accept="image/*">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('avatarUpload').click()"><i class="fas fa-camera"></i> Change Avatar</button>
              </div>
            </div>
            
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($admin['fullname']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="text" name="number" class="form-control" value="<?= htmlspecialchars($admin['number']) ?>">
            </div>
            <div class="form-group">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($admin['city']) ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="margin-top:20px;width:100%;justify-content:center;"><i class="fas fa-save"></i> Save Profile</button>
        </form>
      </div>

      <!-- Security Settings -->
      <div style="display:flex;flex-direction:column;gap:24px;">
      
        <!-- Password -->
        <div class="content-card" style="margin-bottom:0;">
          <h3 class="card-title"><i class="fas fa-shield-alt"></i> Change Password</h3>
          <form method="POST" id="passForm" novalidate>
            <input type="hidden" name="action" value="change_password">
            <div class="form-grid cols-1">
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <div class="password-wrapper">
                   <input type="password" name="current_password" class="form-control" required>
                   <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="password-wrapper">
                   <input type="password" name="new_password" class="form-control" required minlength="6">
                   <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div class="password-wrapper">
                   <input type="password" name="confirm_password" class="form-control" required minlength="6">
                   <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-success" style="margin-top:20px;"><i class="fas fa-key"></i> Update Password</button>
          </form>
        </div>
        
        <!-- System Configuration -->
        <div class="content-card" style="margin-bottom:0;">
          <h3 class="card-title"><i class="fas fa-sliders-h"></i> System Preferences</h3>
          <form method="POST" id="systemSettingsForm" novalidate>
            <input type="hidden" name="action" value="update_system">
            <div class="form-grid">
               <div class="form-group">
                 <label class="form-label">Site Title</label>
                 <input type="text" name="site_title" class="form-control" value="<?= htmlspecialchars($site['site_title']) ?>" required>
               </div>
               <div class="form-group">
                 <label class="form-label">Tagline</label>
                 <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($site['tagline']) ?>">
               </div>
               <div class="form-group">
                 <label class="form-label">Contact Email</label>
                 <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($site['contact_email']) ?>" required>
               </div>
               <div class="form-group">
                 <label class="form-label">Social Media Link</label>
                 <input type="url" name="social_link" class="form-control" value="<?= htmlspecialchars($site['social_link']) ?>">
               </div>
               <div class="form-group">
                 <label class="form-label">Currency Symbol</label>
                 <input type="text" class="form-control" value="₹ (INR)" disabled>
               </div>
               <div class="form-group">
                 <label class="form-label">Dark Mode</label>
                 <input type="text" class="form-control" value="Always On" disabled>
               </div>
            </div>
            <button type="submit" class="btn btn-secondary" style="margin-top:20px; width:100%; justify-content:center;"><i class="fas fa-save"></i> Apply Preferences</button>
          </form>
        </div>
        
      </div>
      
    </div>
  </div>
</div>
<script src="assets/admin.js"></script>
<script>
function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const showError = (el, msg) => {
        el.style.borderColor = '#ef4444';
        const parent = el.closest('.form-group');
        const err = document.createElement('small');
        err.className = 'error-msg';
        err.style.color = '#ef4444';
        err.style.display = 'block';
        err.style.marginTop = '4px';
        err.style.fontWeight = '600';
        err.innerText = msg;
        parent.appendChild(err);
        showToast(msg, 'error');
        el.focus();
    };

    const clearErrors = (form) => {
        form.querySelectorAll('.error-msg').forEach(el => el.remove());
        form.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');
    };

    // Profile Form
    const profileForm = document.getElementById('profileForm');
    if(profileForm) {
        profileForm.addEventListener('submit', function(e) {
            clearErrors(this);
            const name = this.querySelector('[name="fullname"]');
            const email = this.querySelector('[name="email"]');
            if(name.value.trim().length < 3) { e.preventDefault(); showError(name, 'Full name at least 3 chars.'); }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailRegex.test(email.value)) { e.preventDefault(); showError(email, 'Valid email required.'); }
        });
    }

    // Password Form
    const passForm = document.getElementById('passForm');
    if(passForm) {
        passForm.addEventListener('submit', function(e) {
            clearErrors(this);
            const currP = this.querySelector('[name="current_password"]');
            const newP = this.querySelector('[name="new_password"]');
            const confP = this.querySelector('[name="confirm_password"]');
            if(!currP.value) { e.preventDefault(); showError(currP, 'Current password is required.'); return; }
            if(newP.value.length < 6) { e.preventDefault(); showError(newP, 'Min 6 characters required.'); return; }
            if(newP.value !== confP.value) { e.preventDefault(); showError(confP, 'Passwords do not match.'); return; }
        });
    }

    // System Settings
    const sysForm = document.getElementById('systemSettingsForm');
    if(sysForm) {
        sysForm.addEventListener('submit', function(e) {
            clearErrors(this);
            const title = this.querySelector('[name="site_title"]');
            if(title.value.trim().length < 2) { e.preventDefault(); showError(title, 'Site title required.'); }
        });
    }

    // Reset styles on input
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            const parent = this.closest('.form-group');
            const err = parent ? parent.querySelector('.error-msg') : null;
            if(err) err.remove();
        });
    });
});
</script>
</body>
</html>
