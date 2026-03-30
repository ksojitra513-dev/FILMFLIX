<?php
session_start();
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $tag = mysqli_real_escape_string($con, $_POST['tag']);
        $desc = mysqli_real_escape_string($con, $_POST['description']);
        
        $img = '';
        if (!empty($_FILES['image']['name'])) {
            $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['image']['name']);
            if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img);
        }

        $sql = "INSERT INTO gallery (title, category_tag, description, image_url) VALUES ('$title','$tag','$desc','$img')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Gallery item added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM gallery WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Gallery item removed.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $tag = mysqli_real_escape_string($con, $_POST['tag']);
        $desc = mysqli_real_escape_string($con, $_POST['description']);
        
        $img_q = "";
        if (!empty($_FILES['image']['name'])) {
            $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img)) {
                $img_q = ", image_url='$img'";
            }
        }

        $sql = "UPDATE gallery SET title='$title', category_tag='$tag', description='$desc' $img_q WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Item updated!'];
        else $msg = ['type'=>'error', 'text'=>mysqli_error($con)];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
        mysqli_query($con, "UPDATE gallery SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Status updated!'];
    }
}

// Check for status column
try { mysqli_query($con, "ALTER TABLE gallery ADD COLUMN status VARCHAR(20) DEFAULT 'active'"); } catch(Exception $e) {}

$gallery = mysqli_query($con, "SELECT * FROM gallery ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Gallery</title>
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
        <h1 class="page-title">Gallery</h1>
        <p class="page-subtitle">Manage discover page gallery items</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Item</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;">
    <?php while($g = mysqli_fetch_assoc($gallery)): ?>
    <div class="content-card" style="padding:0;overflow:hidden;">
      <div style="height:160px;background:#1a1a28;">
        <?php if($g['image_url']): ?>
        <img src="../<?= htmlspecialchars($g['image_url']) ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='';this.style.background='#1a1a28'">
        <?php endif; ?>
      </div>
      <div style="padding:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
           <span style="font-size:10px;text-transform:uppercase;color:var(--accent);font-weight:700;"><?= htmlspecialchars($g['category_tag']) ?></span>
           <span class="status-badge <?= strtolower($g['status'] ?? 'active') ?>" style="font-size:9px; padding:2px 6px;"><?= ucfirst($g['status'] ?? 'active') ?></span>
        </div>
        <h3 style="font-size:15px;margin:4px 0 8px;"><?= htmlspecialchars($g['title']) ?></h3>
        <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= htmlspecialchars($g['description']) ?></p>
        <div style="display:flex; gap:6px; margin-top:8px;">
          <button type="button" class="btn btn-sm" style="flex:1;background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Large" onclick="openViewGalleryModal('<?= htmlspecialchars(addslashes($g['title'])) ?>', '../<?= htmlspecialchars(addslashes($g['image_url'])) ?>', '<?= htmlspecialchars(addslashes($g['description'])) ?>', '<?= htmlspecialchars(addslashes($g['category_tag'])) ?>')"><i class="fas fa-eye"></i></button>
          <button type="button" class="btn btn-sm btn-primary" style="flex:1;" title="Edit" onclick="openEditGalleryModal(<?= $g['id'] ?>, '<?= htmlspecialchars(addslashes($g['title'])) ?>', '<?= htmlspecialchars(addslashes($g['category_tag'])) ?>', '<?= htmlspecialchars(addslashes($g['description'])) ?>')"><i class="fas fa-edit"></i></button>
          
          <form method="POST" style="flex:1;display:flex;">
             <input type="hidden" name="action" value="update_status">
             <input type="hidden" name="id" value="<?= $g['id'] ?>">
             <input type="hidden" name="status" value="<?= $g['status'] ?? 'active' ?>">
             <button type="submit" class="btn btn-sm" style="width:100%;background:rgba(245,158,11,0.15);color:#f59e0b;border:none;" title="Toggle Visibility"><i class="fas fa-ban"></i></button>
          </form>

          <form method="POST" onsubmit="return confirm('Delete this gallery item?')" style="flex:1;display:flex;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $g['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" style="width:100%;" title="DeletePermanently"><i class="fas fa-trash"></i></button>
          </form>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Gallery Item</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addGalleryForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" placeholder="Item title">
          </div>
          <div class="form-group">
            <label class="form-label">Category Tag (e.g. action, romance)</label>
            <input type="text" name="tag" class="form-control" placeholder="Category tag">
          </div>
          <div class="form-group">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Brief description"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Item</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Gallery Item</span>
      <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editGalleryForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="edit_title" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Category Tag</label>
            <input type="text" name="tag" id="edit_tag" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Update Image (Optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Item</button>
      </div>
    </form>
  </div>
</div>

<!-- View Modal -->
<div class="modal-overlay" id="viewGalleryModal">
  <div class="modal" style="max-width:600px;">
    <div class="modal-header">
      <span class="modal-title">Gallery Preview</span>
      <button class="modal-close" onclick="closeModal('viewGalleryModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;">
       <div style="width:100%; border-radius:12px; overflow:hidden; margin-bottom:20px; border:1px solid var(--border); box-shadow:0 10px 40px rgba(0,0,0,0.5);">
          <img id="view_img" src="" style="width:100%; max-height:400px; object-fit:contain; background:#000;">
       </div>
       <div style="padding:0 10px;">
           <span id="view_tag" style="background:rgba(168,85,247,0.1); color:#a855f7; font-size:10px; font-weight:800; padding:4px 12px; border-radius:50px; text-transform:uppercase; letter-spacing:1px;"></span>
           <h3 id="view_title" style="font-size:24px; font-weight:800; margin:15px 0 10px; color:#fff;"></h3>
           <p id="view_desc" style="color:var(--text-secondary); line-height:1.6; font-size:14px;"></p>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewGalleryModal')">Close Preview</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openViewGalleryModal(title, img, desc, tag) {
    document.getElementById('view_title').innerText = title;
    document.getElementById('view_img').src = img;
    document.getElementById('view_desc').innerText = desc;
    document.getElementById('view_tag').innerText = tag;
    openModal('viewGalleryModal');
}
function openEditGalleryModal(id, title, tag, desc) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_tag').value = tag;
    document.getElementById('edit_desc').value = desc;
    openModal('editModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const forms = [document.getElementById('addGalleryForm'), document.getElementById('editGalleryForm')];
    
    forms.forEach(form => {
        if(form) {
            form.addEventListener('submit', function(e) {
                let valid = true;
                form.querySelectorAll('.error-msg').forEach(el => el.remove());
                form.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

                const showError = (el, msg) => {
                    el.style.borderColor = '#ef4444';
                    const err = document.createElement('small');
                    err.className = 'error-msg';
                    err.style.color = '#ef4444';
                    err.style.display = 'block';
                    err.style.marginTop = '4px';
                    err.style.fontWeight = '600';
                    err.innerText = msg;
                    el.parentNode.appendChild(err);
                    if(valid) {
                        el.focus();
                        showToast(msg, 'error');
                    }
                    valid = false;
                };

                const title = form.querySelector('[name="title"]');
                const tag = form.querySelector('[name="tag"]');
                const img = form.querySelector('[name="image"]');

                if(title.value.trim().length < 2) showError(title, 'Title is required (min 2 chars).');
                if(tag.value.trim().length < 2) showError(tag, 'Category tag is required.');
                
                if(img.files.length > 0) {
                    const file = img.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                    if(!validTypes.includes(file.type)) showError(img, 'Valid image (JPEG, PNG, WEBP) required.');
                    if(file.size > 5 * 1024 * 1024) showError(img, 'Image size should be < 5MB.');
                } else if(form.id === 'addGalleryForm') {
                    showError(img, 'Image upload is required.');
                }

                if(!valid) e.preventDefault();
            });

            form.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.style.borderColor = '';
                    const err = this.parentNode.querySelector('.error-msg');
                    if(err) err.remove();
                });
            });
        }
    });
});
</script>
</body>
</html>
