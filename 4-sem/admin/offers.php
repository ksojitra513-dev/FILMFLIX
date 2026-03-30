<?php
session_start();
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $orig = (float)$_POST['orig'];
        $disc = (float)$_POST['disc'];
        $tag = mysqli_real_escape_string($con, $_POST['tag']);
        $exp = mysqli_real_escape_string($con, $_POST['exp']);
        
        $img = '';
        if (!empty($_FILES['image']['name'])) {
            $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['image']['name']);
            if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img);
        }

        $sql = "INSERT INTO movie_offers (movie_name, movie_image, original_price, discount_price, offer_tag, expiry_date) 
                VALUES ('$name','$img',$orig,$disc,'$tag','$exp')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Offer added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM movie_offers WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Offer removed.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $orig = (float)$_POST['orig'];
        $disc = (float)$_POST['disc'];
        $tag = mysqli_real_escape_string($con, $_POST['tag']);
        $exp = mysqli_real_escape_string($con, $_POST['exp']);
        
        $img_q = "";
        if (!empty($_FILES['image']['name'])) {
            $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img)) {
                $img_q = ", movie_image='$img'";
            }
        }

        $sql = "UPDATE movie_offers SET movie_name='$name', original_price=$orig, discount_price=$disc, offer_tag='$tag', expiry_date='$exp' $img_q WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success', 'text'=>'Offer updated!'];
        else $msg = ['type'=>'error', 'text'=>mysqli_error($con)];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
        mysqli_query($con, "UPDATE movie_offers SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success', 'text'=>'Offer status changed!'];
    }
}

// Check for status column
try { mysqli_query($con, "ALTER TABLE movie_offers ADD COLUMN status VARCHAR(20) DEFAULT 'active'"); } catch(Exception $e) {}

$offers = mysqli_query($con, "SELECT * FROM movie_offers ORDER BY expiry_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Offers</title>
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
        <h1 class="page-title">Offers</h1>
        <p class="page-subtitle">Manage promotional offers and combos</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Offer</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <table class="data-table">
        <thead><tr><th>Movie</th><th>Tag</th><th>Orig. Price</th><th>Disc. Price</th><th>Expires</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($o = mysqli_fetch_assoc($offers)): ?>
        <?php $is_exp = strtotime($o['expiry_date']) < time(); ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <?php if($o['movie_image']): ?>
              <img src="../<?= htmlspecialchars($o['movie_image']) ?>" class="img-thumb" onerror="this.style.display='none'">
              <?php endif; ?>
              <?= htmlspecialchars($o['movie_name']) ?>
            </div>
          </td>
          <td><span class="badge-seats"><?= htmlspecialchars($o['offer_tag']) ?></span></td>
          <td style="text-decoration:line-through;color:var(--text-muted)">₹<?= number_format($o['original_price'],2) ?></td>
          <td style="color:#10b981;font-weight:700;">₹<?= number_format($o['discount_price'],2) ?></td>
          <td>
             <?php if($is_exp): ?> <span style="color:#ef4444;">Expired</span>
             <?php else: ?> <?= date('d M Y', strtotime($o['expiry_date'])) ?> <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:4px;">
              <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Detail" onclick="openViewOfferModal('<?= htmlspecialchars(addslashes($o['movie_name'])) ?>', '../<?= htmlspecialchars(addslashes($o['movie_image'] ?? '')) ?>', '<?= $o['original_price'] ?>', '<?= $o['discount_price'] ?>', '<?= htmlspecialchars(addslashes($o['offer_tag'])) ?>', '<?= date('F j, Y', strtotime($o['expiry_date'])) ?>')"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-primary" title="Edit Offer" onclick="openEditOfferModal(<?= $o['id'] ?>, '<?= htmlspecialchars(addslashes($o['movie_name'])) ?>', '<?= $o['original_price'] ?>', '<?= $o['discount_price'] ?>', '<?= htmlspecialchars(addslashes($o['offer_tag'])) ?>', '<?= $o['expiry_date'] ?>')"><i class="fas fa-edit"></i></button>
              <form method="POST" style="display:inline;">
                 <input type="hidden" name="action" value="update_status">
                 <input type="hidden" name="id" value="<?= $o['id'] ?>">
                 <input type="hidden" name="status" value="<?= $o['status'] ?? 'active' ?>">
                 <button type="submit" class="btn btn-sm" title="Toggle Active Status" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:none;"><i class="fas fa-ban"></i></button>
              </form>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this promotional offer?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Offer</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addOfferForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Movie / Offer Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group full">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
          </div>
          <div class="form-group">
            <label class="form-label">Original Price (₹)</label>
            <input type="number" name="orig" step="0.01" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Discount Price (₹)</label>
            <input type="number" name="disc" step="0.01" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Offer Tag</label>
            <input type="text" name="tag" class="form-control" placeholder="e.g. 50% OFF" required>
          </div>
          <div class="form-group">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="exp" class="form-control" required>
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

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Promotional Offer</span>
      <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editOfferForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Movie / Offer Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Flash Sale">
          </div>
          <div class="form-group full">
            <label class="form-label">Update Image (Optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label class="form-label">Original Price (₹)</label>
            <input type="number" name="orig" id="edit_orig" step="0.01" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Discount Price (₹)</label>
            <input type="number" name="disc" id="edit_disc" step="0.01" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Offer Tag</label>
            <input type="text" name="tag" id="edit_tag" class="form-control" placeholder="BUY1GET1">
          </div>
          <div class="form-group">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="exp" id="edit_exp" class="form-control">
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

<!-- View Modal -->
<div class="modal-overlay" id="viewOfferModal">
  <div class="modal" style="max-width:500px;">
    <div class="modal-header">
      <span class="modal-title">Promotion Preview</span>
      <button class="modal-close" onclick="closeModal('viewOfferModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;">
       <div id="view_offer_banner" style="width:100%; height:200px; border-radius:12px; overflow:hidden; border:1px solid var(--border); position:relative; margin-bottom:20px; box-shadow:0 15px 30px rgba(0,0,0,0.4);">
          <img id="view_img" src="" style="width:100%; height:100%; object-fit:cover;">
          <div id="view_tag" style="position:absolute; top:15px; left:15px; background:var(--accent); color:#fff; font-size:12px; font-weight:800; padding:4px 12px; border-radius:4px; box-shadow:0 4px 10px rgba(6,182,212,0.4);"></div>
       </div>
       <h2 id="view_title" style="font-size:26px; font-weight:800; color:#fff; margin-bottom:5px;"></h2>
       <p id="view_date" style="color:var(--text-muted); font-size:12px; margin-bottom:20px;"></p>
       
       <div style="background:rgba(255,255,255,0.03); padding:20px; border-radius:16px; border:1px solid rgba(255,255,255,0.05); display:inline-block; min-width:200px;">
          <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; margin-bottom:5px;">Exclusive Price</div>
          <div style="display:flex; align-items:center; justify-content:center; gap:15px;">
             <span id="view_orig" style="text-decoration:line-through; color:var(--text-muted); font-size:18px;"></span>
             <span id="view_disc" style="color:#10b981; font-size:32px; font-weight:900;"></span>
          </div>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewOfferModal')">Close Preview</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openViewOfferModal(title, img, orig, disc, tag, date) {
    document.getElementById('view_title').innerText = title;
    document.getElementById('view_img').src = img;
    document.getElementById('view_orig').innerText = "₹" + parseFloat(orig).toLocaleString();
    document.getElementById('view_disc').innerText = "₹" + parseFloat(disc).toLocaleString();
    document.getElementById('view_tag').innerText = tag;
    document.getElementById('view_date').innerText = "Valid until: " + date;
    openModal('viewOfferModal');
}

function openEditOfferModal(id, name, orig, disc, tag, exp) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_orig').value = orig;
    document.getElementById('edit_disc').value = disc;
    document.getElementById('edit_tag').value = tag;
    document.getElementById('edit_exp').value = exp;
    openModal('editModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const forms = [document.getElementById('addOfferForm'), document.getElementById('editOfferForm')];
    
    forms.forEach(form => {
        if(form) {
            form.addEventListener('submit', function(e) {
                let valid = true;
                form.querySelectorAll('.error-msg').forEach(el => el.remove());
                form.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

                const showError = (el, msg) => {
                    el.style.borderColor = '#ef4444';
                    const parent = el.closest('.form-group');
                    const err = document.createElement('small');
                    err.className = 'error-msg';
                    err.innerText = msg;
                    if(parent) parent.appendChild(err);
                    if(valid) {
                        el.focus();
                        showToast(msg, 'error');
                    }
                    valid = false;
                };

                const name = form.querySelector('[name="name"]');
                const orig = form.querySelector('[name="orig"]');
                const disc = form.querySelector('[name="disc"]');
                const tag = form.querySelector('[name="tag"]');
                const exp = form.querySelector('[name="exp"]');
                const img = form.querySelector('[name="image"]');

                if (name.value.trim().length < 3) showError(name, 'Promotion name is required (min 3 chars).');
                
                if (!orig.value || parseFloat(orig.value) <= 0) showError(orig, 'Valid original price is required.');
                if (!disc.value || parseFloat(disc.value) <= 0) showError(disc, 'Valid discount price is required.');
                else if (parseFloat(disc.value) >= parseFloat(orig.value)) showError(disc, 'Discount must be less than original.');
                
                if (tag.value.trim().length < 2) showError(tag, 'Offer tag is required (e.g. 50% OFF).');
                
                if (!exp.value) showError(exp, 'Expiry date is required.');
                else {
                   const today = new Date().setHours(0,0,0,0);
                   const expDay = new Date(exp.value).setHours(0,0,0,0);
                   if (expDay < today) showError(exp, 'Expiry date cannot be in the past.');
                }

                if (img && img.files.length > 0) {
                    const file = img.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                    if (!validTypes.includes(file.type)) showError(img, 'Please upload a valid image (JPEG, PNG, WEBP).');
                    if (file.size > 2 * 1024 * 1024) showError(img, 'Image size should be less than 2MB.');
                } else if (form.id === 'addOfferForm') {
                    showError(img, 'Promotional image is required.');
                }

                if (!valid) {
                    e.preventDefault();
                    showToast('Please check all fields', 'error');
                }
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
});
</script>
</body>
</html>
