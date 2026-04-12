<?php
require_once 'includes/auth.php';
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $address = mysqli_real_escape_string($con, $_POST['address']);
        $city = mysqli_real_escape_string($con, $_POST['city']);
        $state = mysqli_real_escape_string($con, $_POST['state']);
        $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
        $phone = mysqli_real_escape_string($con, $_POST['phone']);
        $total_screens = (int)$_POST['total_screens'];
        $amenities = mysqli_real_escape_string($con, $_POST['amenities']);
        $status = mysqli_real_escape_string($con, $_POST['status']);
        
        $image_url = 'NULL';
        if (!empty($_POST['image_url'])) {
            $image_url = "'" . mysqli_real_escape_string($con, $_POST['image_url']) . "'";
        }

        $sql = "INSERT INTO theaters (name, address, city, state, pincode, phone, total_screens, amenities, status, image_url) 
                VALUES ('$name','$address','$city','$state','$pincode','$phone',$total_screens,'$amenities','$status', $image_url)";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Theater added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM theaters WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Theater record removed.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $address = mysqli_real_escape_string($con, $_POST['address']);
        $city = mysqli_real_escape_string($con, $_POST['city']);
        $state = mysqli_real_escape_string($con, $_POST['state']);
        $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
        $phone = mysqli_real_escape_string($con, $_POST['phone']);
        $total_screens = (int)$_POST['total_screens'];
        $amenities = mysqli_real_escape_string($con, $_POST['amenities']);
        $status = mysqli_real_escape_string($con, $_POST['status']);
        
        $image_url_update = "image_url=NULL";
        if (!empty($_POST['image_url'])) {
            $image_url_val = mysqli_real_escape_string($con, $_POST['image_url']);
            $image_url_update = "image_url='$image_url_val'";
        }

        $sql = "UPDATE theaters SET name='$name', address='$address', city='$city', state='$state', pincode='$pincode', phone='$phone', 
                total_screens=$total_screens, amenities='$amenities', status='$status', $image_url_update WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Theater updated!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
        mysqli_query($con, "UPDATE theaters SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Theater status updated!'];
    }
}

$theaters = mysqli_query($con, "SELECT * FROM theaters ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Theaters</title>
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
        <h1 class="page-title">Theaters</h1>
        <p class="page-subtitle">Manage partner and local theaters</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Theater</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="tSearch" class="form-control" placeholder="Search theaters..."></div>
      </div>
      <div class="table-wrapper">
        <table class="data-table" id="tTable">
          <thead><tr>
            <th>#</th><th>Name</th><th>Location</th><th>Contact</th><th>Screens</th><th>Amenities</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php $i=1; while($t = mysqli_fetch_assoc($theaters)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td style="font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($t['name']) ?></td>
            <td><?= htmlspecialchars($t['city']) ?>, <?= htmlspecialchars($t['state']) ?><?php if($t['pincode']) echo ' - ' . htmlspecialchars($t['pincode']); ?></td>
            <td><?= htmlspecialchars($t['phone']) ?></td>
            <td><span class="badge-seats"><?= $t['total_screens'] ?> Screens</span></td>
            <td><?= htmlspecialchars($t['amenities']) ?></td>
            <td><span class="status-badge <?= strtolower($t['status']) ?>"><?= ucfirst($t['status']) ?></span></td>
            <td>
              <div style="display:flex;gap:4px;">
                <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Theater" onclick="openViewTheaterModal('<?= htmlspecialchars(addslashes($t['name'])) ?>', '<?= htmlspecialchars(addslashes($t['address'])) ?>', '<?= htmlspecialchars(addslashes($t['city'])) ?>', '<?= htmlspecialchars(addslashes($t['state'])) ?>', '<?= htmlspecialchars(addslashes($t['pincode'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['phone'])) ?>', <?= $t['total_screens'] ?>, '<?= htmlspecialchars(addslashes($t['amenities'])) ?>', '<?= htmlspecialchars(addslashes($t['image_url'] ?? '')) ?>', '<?= $t['status'] ?>')"><i class="fas fa-eye"></i></button>
                <button type="button" class="btn btn-sm btn-primary" title="Edit Theater" onclick="openEditTheaterModal(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['name'])) ?>', '<?= htmlspecialchars(addslashes($t['city'])) ?>', '<?= htmlspecialchars(addslashes($t['state'])) ?>', '<?= htmlspecialchars(addslashes($t['pincode'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($t['phone'])) ?>', <?= $t['total_screens'] ?>, '<?= htmlspecialchars(addslashes($t['address'])) ?>', '<?= htmlspecialchars(addslashes($t['amenities'])) ?>', '<?= htmlspecialchars(addslashes($t['image_url'] ?? '')) ?>', '<?= $t['status'] ?>')"><i class="fas fa-edit"></i></button>
                
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <input type="hidden" name="status" value="<?= $t['status'] ?>">
                    <button type="submit" class="btn btn-sm" title="Toggle Status" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:none;"><i class="fas fa-ban"></i></button>
                </form>

                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete theater record?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title"><i class="fas fa-building" style="color:#a855f7;margin-right:8px;"></i>Add Theater</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addTheaterForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Total Screens</label>
            <input type="number" name="total_screens" class="form-control" min="1" value="1" required>
          </div>
          <div class="form-group full">
            <label class="form-label">Image URL</label>
            <input type="text" name="image_url" class="form-control" placeholder="Optional image URL for theater">
          </div>
          <div class="form-group full">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group full">
            <label class="form-label">Amenities</label>
            <input type="text" name="amenities" class="form-control" placeholder="e.g. 3D, Food Court, Recliner">
          </div>
          <div class="form-group full">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
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
      <span class="modal-title"><i class="fas fa-edit" style="color:#a855f7;margin-right:8px;"></i>Edit Theater</span>
      <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editTheaterForm" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="edit_name" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" id="edit_city" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" name="state" id="edit_state" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" id="edit_pincode" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" id="edit_phone" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Total Screens</label>
            <input type="number" name="total_screens" id="edit_screens" class="form-control" min="1">
          </div>
          <div class="form-group full">
            <label class="form-label">Image URL</label>
            <input type="text" name="image_url" id="edit_image_url" class="form-control" placeholder="Optional image URL for theater">
          </div>
          <div class="form-group full">
            <label class="form-label">Address</label>
            <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group full">
            <label class="form-label">Amenities</label>
            <input type="text" name="amenities" id="edit_amenities" class="form-control">
          </div>
          <div class="form-group full">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-control">
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

<!-- View Modal -->
<div class="modal-overlay" id="viewTheaterModal">
  <div class="modal" style="max-width:550px;">
    <div class="modal-header">
      <span class="modal-title">Theater Profile</span>
      <button class="modal-close" onclick="closeModal('viewTheaterModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
       <div style="background:linear-gradient(135deg,rgba(168,85,247,0.1),rgba(6,182,212,0.1)); border-radius:16px; padding:30px; text-align:center; border:1px solid var(--border); margin-bottom:25px; position:relative; overflow:hidden;">
          <div id="view_image_bg" style="position:absolute; top:0; left:0; width:100%; height:100%; opacity:0.15; object-fit:cover; mix-blend-mode:luminosity;"></div>
          <div style="width:70px; height:70px; background:var(--accent); border-radius:18px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:32px; margin:0 auto 15px; box-shadow:0 10px 20px rgba(6,182,212,0.3); position:relative; z-index:2;">
             <i class="fas fa-building"></i>
          </div>
          <h2 id="view_name" style="font-size:24px; font-weight:800; color:#fff; margin:0; position:relative; z-index:2;"></h2>
          <p id="view_location" style="color:var(--text-secondary); margin-top:5px; position:relative; z-index:2;"></p>
          <div id="view_status_badge" style="margin-top:10px; position:relative; z-index:2;"></div>
       </div>

       <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
          <div class="content-card" style="padding:15px; background:rgba(255,255,255,0.02);">
             <label style="display:block; font-size:10px; color:var(--text-muted); text-transform:uppercase; margin-bottom:5px;">Contact Info</label>
             <div style="color:#fff; font-weight:600;"><i class="fas fa-phone-alt" style="margin-right:8px; color:var(--accent);"></i><span id="view_phone"></span></div>
          </div>
          <div class="content-card" style="padding:15px; background:rgba(255,255,255,0.02);">
             <label style="display:block; font-size:10px; color:var(--text-muted); text-transform:uppercase; margin-bottom:5px;">Capacity</label>
             <div style="color:#fff; font-weight:600;"><i class="fas fa-tv" style="margin-right:8px; color:var(--accent-purple);"></i><span id="view_screens"></span> Screens</div>
          </div>
       </div>

       <div class="content-card" style="padding:15px; margin-top:20px; background:rgba(255,255,255,0.02);">
          <label style="display:block; font-size:10px; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Available Amenities</label>
          <div id="view_amenities" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
       </div>

       <div class="content-card" style="padding:15px; margin-top:20px; background:rgba(255,255,255,0.02);">
          <label style="display:block; font-size:10px; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Full Address</label>
          <p id="view_address" style="color:var(--text-secondary); font-size:14px; margin:0; line-height:1.6;"></p>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewTheaterModal')">Done</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
filterTable('tSearch','tTable');

function openViewTheaterModal(name, addr, city, state, pincode, phone, screens, amens, imageUrl, status) {
    document.getElementById('view_name').innerText = name;
    let loc = `${city}, ${state}`;
    if (pincode) loc += ` - ${pincode}`;
    document.getElementById('view_location').innerText = loc;
    document.getElementById('view_phone').innerText = phone;
    document.getElementById('view_screens').innerText = screens;
    document.getElementById('view_address').innerText = addr;
    document.getElementById('view_status_badge').innerHTML = `<span class="status-badge ${status.toLowerCase()}">${status.toUpperCase()}</span>`;
    
    if (imageUrl) {
        document.getElementById('view_image_bg').innerHTML = `<img src="${imageUrl}" style="width:100%; height:100%; object-fit:cover;">`;
    } else {
        document.getElementById('view_image_bg').innerHTML = '';
    }
    
    const amenContainer = document.getElementById('view_amenities');
    amenContainer.innerHTML = '';
    amens.split(',').forEach(a => {
        if(a.trim()) {
            const span = document.createElement('span');
            span.className = 'badge-seats';
            span.style.background = 'rgba(6,182,212,0.1)';
            span.style.color = 'var(--accent)';
            span.innerText = a.trim();
            amenContainer.appendChild(span);
        }
    });
    
    openModal('viewTheaterModal');
}

function openEditTheaterModal(id, name, city, state, pincode, phone, screens, addr, amens, imageUrl, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_city').value = city;
    document.getElementById('edit_state').value = state;
    document.getElementById('edit_pincode').value = pincode;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_screens').value = screens;
    document.getElementById('edit_address').value = addr;
    document.getElementById('edit_amenities').value = amens;
    document.getElementById('edit_image_url').value = imageUrl;
    document.getElementById('edit_status').value = status;
    openModal('editModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const forms = [document.getElementById('addTheaterForm'), document.getElementById('editTheaterForm')];
    
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
                const city = form.querySelector('[name="city"]');
                const state = form.querySelector('[name="state"]');
                const phone = form.querySelector('[name="phone"]');
                const screens = form.querySelector('[name="total_screens"]');
                const addr = form.querySelector('[name="address"]');

                if (name.value.trim().length < 3) showError(name, 'Theater name is required (min 3 chars).');
                if (city.value.trim().length < 2) showError(city, 'City name is required.');
                if (state.value.trim().length < 2) showError(state, 'State is required.');
                if (addr.value.trim().length < 5) showError(addr, 'Full address is required.');
                
                if (parseInt(screens.value) < 1) showError(screens, 'Must have at least 1 screen.');
                
                const phoneRegex = /^[0-9\-\+\s]{10,15}$/;
                if (!phone.value.trim() || !phoneRegex.test(phone.value.trim())) {
                    showError(phone, 'Valid contact number is required.');
                }

                if(!valid) {
                    e.preventDefault();
                    showToast('Please fill all required fields correctly', 'error');
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


