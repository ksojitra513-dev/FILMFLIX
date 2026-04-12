<?php
require_once 'includes/auth.php';
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $subtitle = mysqli_real_escape_string($con, $_POST['subtitle']);
        
        $img = mysqli_real_escape_string($con, $_POST['imagurl']);

        $sql = "INSERT INTO banner (title, subtitle, imagurl) VALUES ('$title','$subtitle','$img')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Banner added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM banner WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Banner deleted.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $subtitle = mysqli_real_escape_string($con, $_POST['subtitle']);
        
        $img = mysqli_real_escape_string($con, $_POST['imagurl']);
        $img_q = ", imagurl='$img'";

        $sql = "UPDATE banner SET title='$title', subtitle='$subtitle' $img_q WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Banner updated!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
        mysqli_query($con, "UPDATE banner SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Banner status updated!'];
    }
}

// Ensure status column exists
try { mysqli_query($con, "ALTER TABLE banner ADD COLUMN status VARCHAR(10) DEFAULT 'active'"); } catch(Exception $e) {}

$banners = mysqli_query($con, "SELECT * FROM banner ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Banners</title>
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
        <h1 class="page-title">Hero Banners</h1>
        <p class="page-subtitle">Manage homepage carousel images</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Banner</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card" style="padding:12px 16px; margin-bottom:20px;">
      <div class="search-box"><i class="fas fa-search"></i><input type="text" id="bannerSearch" class="form-control" placeholder="Search banners by title or subtitle..."></div>
    </div>

    <div id="bannerGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
    <?php while($b = mysqli_fetch_assoc($banners)): ?>
    <div class="content-card" style="padding:0;overflow:hidden;">
      <div style="height:150px;background:#1a1a28;position:relative;">
        <?php if($b['imagurl']): ?>
        <?php $real_img = (strpos($b['imagurl'], 'http') === 0) ? $b['imagurl'] : '../assets/images/' . $b['imagurl']; ?>
        <img src="<?= htmlspecialchars($real_img) ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='';this.style.background='#1a1a28'">
        <?php endif; ?>
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.8),transparent);"></div>
        <div style="position:absolute;bottom:0;left:0;right:0;padding:16px;">
          <h3 style="font-size:16px;color:#fff;font-weight:700;text-shadow:0 2px 4px rgba(0,0,0,0.5);"><?= htmlspecialchars($b['title']) ?></h3>
          <p style="font-size:12px;color:rgba(255,255,255,0.8);"><?= htmlspecialchars($b['subtitle']) ?></p>
        </div>
      </div>
       <div style="padding:16px;display:flex;gap:6px;justify-content:flex-end;">
          <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Details" onclick="openViewBannerModal('<?= htmlspecialchars(addslashes($b['title'])) ?>', '<?= htmlspecialchars(addslashes($b['subtitle'])) ?>', '../<?= htmlspecialchars(addslashes($b['imagurl'])) ?>', '<?= $b['status'] ?? 'active' ?>')"><i class="fas fa-eye"></i></button>
          <button type="button" class="btn btn-sm btn-primary" title="Edit" onclick="openEditBannerModal(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['title'])) ?>', '<?= htmlspecialchars(addslashes($b['subtitle'])) ?>', '<?= $b['status'] ?? 'active' ?>', '<?= htmlspecialchars(addslashes($b['imagurl'] ?? '')) ?>')"><i class="fas fa-edit"></i></button>
          
          <form method="POST" style="display:inline;">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">
            <input type="hidden" name="status" value="<?= $b['status'] ?? 'active' ?>">
            <button type="submit" class="btn btn-sm" title="Toggle Status" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:none;"><i class="fas fa-ban"></i></button>
          </form>

         <form method="POST" style="display:inline;" onsubmit="return confirm('Delete banner?')">
           <input type="hidden" name="action" value="delete">
           <input type="hidden" name="id" value="<?= $b['id'] ?>">
           <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fas fa-trash"></i></button>
         </form>
      </div>
    </div>
    <?php endwhile; ?>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Banner</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addBannerForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Banner Title</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Welcome to FILMFLIX">
          </div>
          <div class="form-group">
            <label class="form-label">Subtitle / Description</label>
            <textarea name="subtitle" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Banner Image URL / Path</label>
            <input type="text" name="imagurl" class="form-control" placeholder="uri.webp or https://..." required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Banner</span>
      <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editBannerForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_banner_id">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Banner Title</label>
            <input type="text" name="title" id="edit_banner_title" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Subtitle / Description</label>
            <textarea name="subtitle" id="edit_banner_subtitle" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Banner Image URL / Path</label>
            <input type="text" name="imagurl" id="edit_banner_img_url" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" id="edit_banner_status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="viewModal">
  <div class="modal" style="max-width:600px;">
    <div class="modal-header">
      <span class="modal-title" id="view_banner_title_header">Banner Details</span>
      <button class="modal-close" onclick="closeModal('viewModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
       <div id="view_banner_img_container" style="width:100%; height:250px; background:#000; border-radius:12px; overflow:hidden; margin-bottom:20px; border:1px solid var(--border);">
          <img id="view_banner_img" src="" style="width:100%; height:100%; object-fit:cover;">
       </div>
       <div class="content-card" style="background:rgba(255,255,255,0.03); border:1px solid var(--border);">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
             <h4 id="view_banner_title" style="font-size:22px; color:var(--accent); margin:0; font-weight:700;"></h4>
             <span id="view_status_badge" class="status-badge"></span>
          </div>
          <p id="view_banner_subtitle" style="color:var(--text-secondary); line-height:1.6; font-size:15px; margin:0;"></p>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" onclick="closeModal('viewModal')">Close</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openViewBannerModal(title, subtitle, img, status) {
    document.getElementById('view_banner_title_header').innerText = "Banner: " + title;
    document.getElementById('view_banner_title').innerText = title;
    document.getElementById('view_banner_subtitle').innerText = subtitle;
    const realImg = (img.indexOf('http') === 0 || img.indexOf('../') === 0) ? img : '../assets/images/' + img;
    document.getElementById('view_banner_img').src = realImg;
    
    const badge = document.getElementById('view_status_badge');
    badge.innerText = status.toUpperCase();
    badge.className = `status-badge ${status.toLowerCase()}`;
    
    openModal('viewModal');
}
function openEditBannerModal(id, title, subtitle, status, imgurl) {
    document.getElementById('edit_banner_id').value = id;
    document.getElementById('edit_banner_title').value = title;
    document.getElementById('edit_banner_subtitle').value = subtitle;
    document.getElementById('edit_banner_status').value = status;
    document.getElementById('edit_banner_img_url').value = imgurl;
    openModal('editModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const bannerForms = [document.getElementById('addBannerForm'), document.getElementById('editBannerForm')];

    const showError = (el, msg) => {
        el.style.borderColor = '#ef4444';
        const parent = el.closest('.form-group');
        const err = document.createElement('small');
        err.className = 'error-msg';
        err.innerText = msg;
        if(parent) parent.appendChild(err);
    };

    bannerForms.forEach(form => {
        if(form) {
            form.addEventListener('submit', function(e) {
                let valid = true;
                form.querySelectorAll('.error-msg').forEach(el => el.remove());
                form.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

                const title = form.querySelector('[name="title"]');
                const sub = form.querySelector('[name="subtitle"]');
                const img = form.querySelector('[name="imagurl"]');

                if(title.value.trim().length < 3) { showError(title, 'Title is required.'); valid = false; }
                if(sub.value.trim().length < 5) { showError(sub, 'Subtitle required.'); valid = false; }
                if(img.value.trim().length === 0) { showError(img, 'Image URL/path is required.'); valid = false; }

                if(!valid) { e.preventDefault(); showToast('Please fix errors', 'error'); }
            });

            form.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.style.borderColor = '';
                    const parent = this.closest('.form-group');
                    const err = parent ? parent.querySelector('.error-msg') : null;
                    if(err) err.remove();
                });
            });
        }
    });

    const searchInput = document.getElementById('bannerSearch');
    const bannerGrid = document.getElementById('bannerGrid');
    
    if (searchInput && bannerGrid) {
        const cards = bannerGrid.querySelectorAll('.content-card');
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const subtitle = card.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || subtitle.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
</body>
</html>


