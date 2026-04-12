<?php
require_once 'includes/auth.php';
require_once '../config.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $theater_id = (int)$_POST['theater_id'];
        $name = mysqli_real_escape_string($con, $_POST['screen_name']);
        $capacity = (int)$_POST['capacity'];
        $type = mysqli_real_escape_string($con, $_POST['screen_type']);
        
        $seat_layout = 'NULL';
        if (!empty($_POST['seat_layout'])) {
            $layout_val = trim($_POST['seat_layout']);
            json_decode($layout_val);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $layout_val = json_encode($layout_val);
            }
            $seat_layout = "'" . mysqli_real_escape_string($con, $layout_val) . "'";
        }
        
        $sql = "INSERT INTO screens (theater_id, screen_name, capacity, screen_type, seat_layout) VALUES ($theater_id, '$name', $capacity, '$type', $seat_layout)";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Screen added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM screens WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Screen deleted.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $theater_id = (int)$_POST['theater_id'];
        $name = mysqli_real_escape_string($con, $_POST['screen_name']);
        $capacity = (int)$_POST['capacity'];
        $type = mysqli_real_escape_string($con, $_POST['screen_type']);
        
        $seat_layout = 'NULL';
        if (!empty($_POST['seat_layout'])) {
            $layout_val = trim($_POST['seat_layout']);
            json_decode($layout_val);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $layout_val = json_encode($layout_val);
            }
            $seat_layout = "'" . mysqli_real_escape_string($con, $layout_val) . "'";
        }
        
        $sql = "UPDATE screens SET theater_id=$theater_id, screen_name='$name', capacity=$capacity, screen_type='$type', seat_layout=$seat_layout WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Screen updated!'];
        else $msg = ['type'=>'error', 'text'=>mysqli_error($con)];
    }
}

$screens = mysqli_query($con, "SELECT s.*, t.name as theater_name FROM screens s JOIN theaters t ON s.theater_id = t.id ORDER BY s.id DESC");
$theaters = mysqli_query($con, "SELECT id, name FROM theaters");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Screens</title>
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
        <h1 class="page-title">Theater Screens</h1>
        <p class="page-subtitle">Manage screens for each theater</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Screen</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="screenSearch" class="form-control" placeholder="Search screens..."></div>
      </div>
      <div class="table-wrapper">
        <table class="data-table" id="screenTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Theater</th>
              <th>Screen Name</th>
              <th>Capacity</th>
              <th>Type</th>
              <th>Seat Layout</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($s = mysqli_fetch_assoc($screens)): ?>
            <tr>
              <td><span class="booking-id">#<?= $s['id'] ?></span></td>
              <td><?= htmlspecialchars($s['theater_name']) ?></td>
              <td style="font-weight:600;"><?= htmlspecialchars($s['screen_name']) ?></td>
              <td><span class="badge-seats"><?= $s['capacity'] ?> Seats</span></td>
              <td><span class="status-badge active"><?= $s['screen_type'] ?></span></td>
              <td><?= $s['seat_layout'] ? '<span class="status-badge" style="background:rgba(168,85,247,0.15);color:#a855f7;">Custom</span>' : '<span style="color:var(--text-muted);">Standard</span>' ?></td>
              <td>
                <div style="display:flex; gap:6px;">
                  <button class="btn btn-sm btn-primary btn-icon" onclick="openEditModal(<?= $s['id'] ?>, <?= $s['theater_id'] ?>, '<?= htmlspecialchars(addslashes($s['screen_name'])) ?>', <?= $s['capacity'] ?>, '<?= $s['screen_type'] ?>', '<?= htmlspecialchars(addslashes($s['seat_layout'] ?? '')) ?>')"><i class="fas fa-edit"></i></button>
                  <form method="POST" onsubmit="return confirm('Delete screen?')" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
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
      <span class="modal-title">Add New Screen</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addScreenForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Theater</label>
            <select name="theater_id" class="form-control" required>
              <option value="">Select Theater</option>
              <?php mysqli_data_seek($theaters, 0); while($t = mysqli_fetch_assoc($theaters)): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Screen Name</label>
            <input type="text" name="screen_name" class="form-control" placeholder="Screen 1" required>
          </div>
          <div class="form-group">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" value="60" required>
          </div>
          <div class="form-group full">
            <label class="form-label">Screen Type</label>
            <select name="screen_type" class="form-control">
              <option value="2D">2D</option>
              <option value="3D">3D</option>
              <option value="IMAX">IMAX</option>
              <option value="4DX">4DX</option>
            </select>
          </div>
          <div class="form-group full">
            <label class="form-label">Seat Layout (Optional JSON/String)</label>
            <textarea name="seat_layout" class="form-control" rows="2" placeholder="e.g. standard layout or custom mapping"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Screen</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModalOverlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Screen</span>
      <button class="modal-close" onclick="closeModal('editModalOverlay')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editScreenForm" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Theater</label>
            <select name="theater_id" id="edit_theater_id" class="form-control" required>
              <?php mysqli_data_seek($theaters, 0); while($t = mysqli_fetch_assoc($theaters)): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Screen Name</label>
            <input type="text" name="screen_name" id="edit_screen_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" id="edit_capacity" class="form-control" required>
          </div>
          <div class="form-group full">
            <label class="form-label">Screen Type</label>
            <select name="screen_type" id="edit_screen_type" class="form-control">
              <option value="2D">2D</option>
              <option value="3D">3D</option>
              <option value="IMAX">IMAX</option>
              <option value="4DX">4DX</option>
            </select>
          </div>
          <div class="form-group full">
            <label class="form-label">Seat Layout (Optional JSON/String)</label>
            <textarea name="seat_layout" id="edit_seat_layout" class="form-control" rows="2"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModalOverlay')">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Screen</button>
      </div>
    </form>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
filterTable('screenSearch', 'screenTable');

function openEditModal(id, tid, name, cap, type, layout) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_theater_id').value = tid;
    document.getElementById('edit_screen_name').value = name;
    document.getElementById('edit_capacity').value = cap;
    document.getElementById('edit_screen_type').value = type;
    document.getElementById('edit_seat_layout').value = layout;
    openModal('editModalOverlay');
}

document.addEventListener('DOMContentLoaded', () => {
    const forms = [document.getElementById('addScreenForm'), document.getElementById('editScreenForm')];
    
    forms.forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                let valid = true;
                form.querySelectorAll('.error-msg').forEach(el => el.remove());
                form.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

                const showError = (el, msg) => {
                    el.style.borderColor = '#ef4444';
                    const parent = el.closest('.form-group');
                    if (parent) {
                        const err = document.createElement('small');
                        err.className = 'error-msg';
                        err.style.color = '#ef4444';
                        err.style.display = 'block';
                        err.style.marginTop = '4px';
                        err.style.fontWeight = '600';
                        err.style.fontSize = '12px';
                        err.innerText = msg;
                        parent.appendChild(err);
                    }
                    if (valid) {
                        el.focus();
                        if (typeof showToast === 'function') showToast(msg, 'error');
                    }
                    valid = false;
                };

                const theaterId = form.querySelector('[name="theater_id"]');
                const screenName = form.querySelector('[name="screen_name"]');
                const capacity = form.querySelector('[name="capacity"]');

                if (!theaterId.value.trim()) showError(theaterId, 'Please select a theater from the list.');
                if (screenName.value.trim().length < 2) showError(screenName, 'Screen name must be at least 2 characters.');
                
                const capVal = parseInt(capacity.value);
                if (isNaN(capVal) || capVal < 1) showError(capacity, 'Capacity must be at least 1 seat.');

                if (!valid) {
                    e.preventDefault();
                }
            });

            form.querySelectorAll('.form-control').forEach(input => {
                const clearError = function() {
                    this.style.borderColor = '';
                    const parent = this.closest('.form-group');
                    const err = parent ? parent.querySelector('.error-msg') : null;
                    if(err) err.remove();
                };
                input.addEventListener('input', clearError);
                input.addEventListener('change', clearError);
            });
        }
    });
});
</script>
</body>
</html>
