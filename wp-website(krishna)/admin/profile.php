<?php 
include 'header.php'; 

// Fetch current admin (since there's no real session, we take the first 'Admin' user)
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'Admin' ORDER BY id ASC LIMIT 1");
$admin = $stmt->fetch();

if (!$admin) {
    // Fallback if no admin was found
    $admin = [
        'id' => 36,
        'name' => 'floder',
        'email' => 'floder@admin.com',
        'city' => 'Admin City',
        'number' => '0000000000',
        'img' => '1.jpg',
        'role' => 'Admin'
    ];
}
?>

<div class="content-panel fade-in">
    <div class="panel-header">
        <h2 style="color: var(--text-main);"><i class="fas fa-user-circle" style="color: var(--primary-color); margin-right: 0.75rem;"></i> My Admin Profile</h2>
        <div style="font-size: 0.85rem; color: var(--text-muted);">Manage your account details and security settings</div>
    </div>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 2.5rem; margin-top: 2rem;">
        
        <!-- Left: Avatar Card -->
        <div style="background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: 24px; padding: 2.5rem 2rem; text-align: center; height: fit-content;">
            <div style="position: relative; width: 150px; height: 150px; margin: 0 auto 1.5rem;">
                <img id="profile-preview" src="<?= !empty($admin['img']) && !str_starts_with($admin['img'], 'http') ? 'uploads/'.$admin['img'] : 'https://ui-avatars.com/api/?name='.urlencode($admin['name']).'&background=6366f1&color=fff' ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-color); box-shadow: 0 10px 30px rgba(99,102,241,0.25);">
                <label for="profile-upload" style="position: absolute; bottom: 5px; right: 5px; width: 38px; height: 38px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.3);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="profile-upload" accept="image/*" style="display: none;">
                </label>
            </div>
            
            <h3 style="color: var(--text-main); margin-bottom: 0.25rem;"><?= htmlspecialchars($admin['name']) ?></h3>
            <p style="color: var(--primary-color); font-weight: 600; font-size: 0.85rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1px;"><?= htmlspecialchars($admin['role']) ?></p>
            
            <div style="background: rgba(15, 23, 42, 0.4); border-radius: 16px; padding: 1.25rem; text-align: left;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <i class="fas fa-envelope" style="color: var(--text-muted); width: 16px;"></i>
                    <span style="font-size: 0.85rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($admin['email']) ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-map-marker-alt" style="color: var(--text-muted); width: 16px;"></i>
                    <span style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($admin['city']) ?></span>
                </div>
            </div>
        </div>

        <!-- Right: Edit Form -->
        <div style="background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: 24px; padding: 2.5rem;">
            <form id="profile-form">
                <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                <input type="hidden" name="img_existing" value="<?= $admin['img'] ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.6rem;">Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required style="width: 100%; background: rgba(15,23,42,0.4); border: 1px solid var(--border-color); color: #fff; padding: 0.85rem 1rem; border-radius: 12px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.6rem;">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required style="width: 100%; background: rgba(15,23,42,0.4); border: 1px solid var(--border-color); color: #fff; padding: 0.85rem 1rem; border-radius: 12px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.6rem;">City</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($admin['city']) ?>" style="width: 100%; background: rgba(15,23,42,0.4); border: 1px solid var(--border-color); color: #fff; padding: 0.85rem 1rem; border-radius: 12px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.6rem;">Phone Number</label>
                        <input type="text" name="number" value="<?= htmlspecialchars($admin['number']) ?>" style="width: 100%; background: rgba(15,23,42,0.4); border: 1px solid var(--border-color); color: #fff; padding: 0.85rem 1rem; border-radius: 12px; outline: none;">
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-shield-alt" style="color: var(--primary-color);"></i>
                        <span style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">Security</span>
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.6rem;">Change Password (leave blank to keep current)</label>
                        <input type="password" name="password" placeholder="Enter new password..." style="width: 100%; background: rgba(15,23,42,0.4); border: 1px solid var(--border-color); color: #fff; padding: 0.85rem 1rem; border-radius: 12px; outline: none;">
                    </div>
                </div>

                <div style="margin-top: 3rem; display: flex; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 2rem;">
                    <button type="submit" id="save-btn" style="background: var(--primary-color); border: none; color: #fff; padding: 0.85rem 2.5rem; border-radius: 12px; cursor: pointer; font-weight: 700; transition: all 0.2s; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Save Profile
                    </button>
                    <button type="button" onclick="location.reload()" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600;">
                        Discard Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" style="position: fixed; bottom: 2rem; right: 2rem; background: var(--accent); color: #fff; padding: 1rem 1.75rem; border-radius: 15px; font-weight: 600; display: none; align-items: center; gap: 0.75rem; z-index: 10000; box-shadow: 0 10px 40px rgba(16,185,129,0.3);">
    <i class="fas fa-check-circle"></i>
    <span id="toast-msg">Settings updated!</span>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('profile-upload');
    const profilePreview = document.getElementById('profile-preview');
    const form = document.getElementById('profile-form');
    const saveBtn = document.getElementById('save-btn');
    const toast = document.getElementById('toast');

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (re) => {
                profilePreview.src = re.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // --- JS VALIDATION ---
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const number = form.number.value.trim();
        const password = form.password.value.trim();
        
        let errors = [];
        
        if (name.length < 3) errors.push("Full name must be at least 3 characters.");
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) errors.push("Please enter a valid email address.");
        
        if (number && number.length < 10) errors.push("Phone number should be at least 10 digits.");
        
        if (password && password.length < 6) errors.push("Password must be at least 6 characters.");
        
        if (errors.length > 0) {
            showToast(errors[0], true); // Show the first error
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const formData = new FormData(form);
        const file = fileInput.files[0];
        if (file) {
            formData.append('img', file);
        }

        try {
            const res = await fetch('api_users.php?action=update_profile', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.success) {
                showToast('Profile updated effectively!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.error || 'Update failed', true);
            }
        } catch(err) {
            showToast('Network error', true);
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Profile';
        }
    });

    function showToast(msg, isError = false) {
        document.getElementById('toast-msg').textContent = msg;
        toast.style.background = isError ? '#ef4444' : '#10b981';
        toast.style.display = 'flex';
        // Add a slide-up animation effect
        toast.style.animation = 'none';
        void toast.offsetWidth;
        toast.style.animation = 'modalIn 0.3s ease';
        setTimeout(() => { toast.style.display = 'none'; }, 3500);
    }
});
</script>

<?php include 'footer.php'; ?>
