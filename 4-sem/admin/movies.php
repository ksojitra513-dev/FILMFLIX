<?php
session_start();
require_once '../config.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name  = mysqli_real_escape_string($con, $_POST['name']);
        $is_new = (int)($_POST['is_new'] ?? 0);

        // Handle poster upload
        $poster = '';
        if (!empty($_FILES['poster']['name'])) {
            $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $poster = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['poster']['name']);
            $dest = '../' . $poster;
            if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
            move_uploaded_file($_FILES['poster']['tmp_name'], $dest);
        }

        $sql = "INSERT INTO movies (name, `poster img`, is_new) VALUES ('$name','$poster',$is_new)";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Movie added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM movies WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Movie deleted.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $is_new = (int)$_POST['is_new'];
        
        $img_q = "";
        if (!empty($_FILES['poster']['name'])) {
            $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['poster']['name']);
            if (move_uploaded_file($_FILES['poster']['tmp_name'], '../' . $img)) {
                $img_q = ", `poster img`='$img'";
            }
        }

        $sql = "UPDATE movies SET name='$name', is_new=$is_new $img_q WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Movie updated!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
        mysqli_query($con, "UPDATE movies SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Movie status updated!'];
    }
}

// Check for status column
try { mysqli_query($con, "ALTER TABLE movies ADD COLUMN status VARCHAR(20) DEFAULT 'active'"); } catch(Exception $e) {}

$movies = mysqli_query($con, "SELECT * FROM movies ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Movies</title>
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
        <h1 class="page-title">Movies</h1>
        <p class="page-subtitle">Manage your movie library</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Movie</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <!-- Movies Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:20px;">
    <?php while($m = mysqli_fetch_assoc($movies)): ?>
    <div class="content-card" style="padding:0;overflow:hidden;position:relative;">
      <?php if($m['is_new']): ?>
      <span style="position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#a855f7,#06b6d4);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:50px;z-index:1;">NEW</span>
      <?php endif; ?>
      <div style="height:200px;background:#1a1a28;overflow:hidden;">
        <?php
        $poster = $m['poster img'];
        $src = '';
        if (!empty($poster)) {
          if (file_exists('../' . $poster)) $src = '../' . $poster;
          elseif (file_exists('../' . $poster)) $src = '../' . $poster;
          else $src = '../' . $poster;
        }
        ?>
        <?php if($poster): ?>
        <img src="<?= htmlspecialchars('../' . $poster) ?>" alt="<?= htmlspecialchars($m['name']) ?>"
             style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'">
        <?php else: ?>
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.1);font-size:48px;"><i class="fas fa-film"></i></div>
        <?php endif; ?>
      </div>
      <div style="padding:14px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
           <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($m['name']) ?></div>
           <span class="status-badge <?= strtolower($m['status'] ?? 'active') ?>" style="font-size:9px; padding:2px 6px;"><?= ucfirst($m['status'] ?? 'active') ?></span>
        </div>
        <div style="display:flex; gap:6px; margin-top:8px;">
          <button type="button" class="btn btn-sm" style="flex:1;background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Details" onclick="openViewMovieModal('<?= htmlspecialchars(addslashes($m['name'])) ?>', '../<?= htmlspecialchars(addslashes($m['poster img'])) ?>', <?= $m['is_new'] ?>)"><i class="fas fa-eye"></i></button>
          <button type="button" class="btn btn-sm btn-primary" style="flex:1;" title="Edit" onclick="openEditMovieModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>', <?= $m['is_new'] ?>)"><i class="fas fa-edit"></i></button>
          
          <form method="POST" style="flex:1;display:flex;">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">
            <input type="hidden" name="status" value="<?= $m['status'] ?? 'active' ?>">
            <button type="submit" class="btn btn-sm" style="width:100%;background:rgba(245,158,11,0.15);color:#f59e0b;border:none;" title="Toggle Status"><i class="fas fa-ban"></i></button>
          </form>

          <form method="POST" onsubmit="return confirm('Delete this movie?')" style="flex:1;display:flex;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" style="width:100%;" title="Delete"><i class="fas fa-trash"></i></button>
          </form>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
    </div>

  </div>
</div>

<!-- Add Movie Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title"><i class="fas fa-film" style="color:#a855f7;margin-right:8px;"></i>Add Movie</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addMovieForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Movie Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter movie title">
          </div>
          <div class="form-group">
            <label class="form-label">Poster Image</label>
            <input type="file" name="poster" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label class="form-label">Mark as New?</label>
            <select name="is_new" class="form-control">
              <option value="0">No</option>
              <option value="1">Yes</option>
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

<!-- Edit Movie Modal -->
<div class="modal-overlay" id="editMovieModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title"><i class="fas fa-edit" style="color:#a855f7;margin-right:8px;"></i>Edit Movie</span>
      <button class="modal-close" onclick="closeModal('editMovieModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editMovieForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_movie_id">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Movie Name</label>
            <input type="text" name="name" id="edit_movie_name" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Update Poster Image (Optional)</label>
            <input type="file" name="poster" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label class="form-label">Is New Release?</label>
            <select name="is_new" id="edit_movie_is_new" class="form-control">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editMovieModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="viewMovieModal">
  <div class="modal" style="max-width:500px;">
    <div class="modal-header">
      <span class="modal-title">Movie Preview</span>
      <button class="modal-close" onclick="closeModal('viewMovieModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;">
       <div style="width:100%; height:320px; background:#000; border-radius:12px; overflow:hidden; margin-bottom:20px; border:1px solid var(--border); position:relative;">
          <img id="view_movie_poster" src="" style="width:100%; height:100%; object-fit:cover;">
          <span id="view_movie_new_badge" style="position:absolute; top:15px; right:15px; background:linear-gradient(135deg,#a855f7,#06b6d4); color:#fff; font-size:11px; font-weight:800; padding:4px 12px; border-radius:50px; display:none;">NEW RELEASE</span>
       </div>
       <h3 id="view_movie_name" style="font-size:24px; font-weight:800; margin-bottom:10px; color:#fff;"></h3>
       <div style="display:flex; justify-content:center; gap:10px; margin-top:15px;">
          <span class="badge-seats" style="background:rgba(6,182,212,0.1); color:#06b6d4;">4K Ultra HD</span>
          <span class="badge-seats" style="background:rgba(168,85,247,0.1); color:#a855f7;">Dolby Atmos</span>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewMovieModal')">Close Preview</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openViewMovieModal(name, poster, isNew) {
    document.getElementById('view_movie_name').innerText = name;
    document.getElementById('view_movie_poster').src = poster;
    document.getElementById('view_movie_new_badge').style.display = isNew ? 'block' : 'none';
    openModal('viewMovieModal');
}
function openEditMovieModal(id, name, isNew) {
    document.getElementById('edit_movie_id').value = id;
    document.getElementById('edit_movie_name').value = name;
    document.getElementById('edit_movie_is_new').value = isNew;
    openModal('editMovieModal');
}
document.addEventListener('DOMContentLoaded', () => {
    const movieForms = [document.getElementById('addMovieForm'), document.getElementById('editMovieForm')];
    
    movieForms.forEach(form => {
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
                const poster = form.querySelector('[name="poster"]');

                if(name.value.trim().length < 2) showError(name, 'Movie name must be at least 2 characters.');
                
                if(poster.files.length > 0) {
                    const file = poster.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                    if(!validTypes.includes(file.type)) showError(poster, 'Please upload a valid image (JPEG, PNG, WEBP).');
                    if(file.size > 2 * 1024 * 1024) showError(poster, 'Image size should be less than 2MB.');
                } else if(form.id === 'addMovieForm') {
                    showError(poster, 'Poster image is required.');
                }

                if(!valid) e.preventDefault();
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
