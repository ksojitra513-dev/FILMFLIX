<?php
require_once 'includes/auth.php';
require_once '../config.php';

// This is a generic management script for movie category tables
// Expects $table_name and $page_title to be defined before inclusion

if (!isset($table_name)) {
    die("Table name not specified.");
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title     = mysqli_real_escape_string($con, $_POST['title']);
        $language  = mysqli_real_escape_string($con, $_POST['language']);
        $badge     = mysqli_real_escape_string($con, $_POST['badge']);
        $rating    = (float)$_POST['rating'];
        $duration  = mysqli_real_escape_string($con, $_POST['duration']);
        $desc      = mysqli_real_escape_string($con, $_POST['description']);
        
        $img = mysqli_real_escape_string($con, $_POST['image_path']);

        $sql = "INSERT INTO $table_name (title, language, badge, image_path, rating, duration, description) 
                VALUES ('$title', '$language', '$badge', '$img', $rating, '$duration', '$desc')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Movie added to category!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM $table_name WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Movie removed from category.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $title     = mysqli_real_escape_string($con, $_POST['title']);
        $language  = mysqli_real_escape_string($con, $_POST['language']);
        $badge     = mysqli_real_escape_string($con, $_POST['badge']);
        $rating    = (float)$_POST['rating'];
        $duration  = mysqli_real_escape_string($con, $_POST['duration']);
        $desc      = mysqli_real_escape_string($con, $_POST['description']);
        
        $img = mysqli_real_escape_string($con, $_POST['image_path']);
        $img_q = ", image_path='$img'";

        $sql = "UPDATE $table_name SET title='$title', language='$language', badge='$badge', rating=$rating, duration='$duration', description='$desc' $img_q WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Movie updated!'];
        else $msg = ['type'=>'error', 'text'=>mysqli_error($con)];
    }
}

$movies = mysqli_query($con, "SELECT * FROM $table_name ORDER BY id DESC");
$total = mysqli_num_rows($movies);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — <?= $page_title ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
<style>
  .movie-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
  }
  .movie-card:hover { transform: translateY(-3px); border-color: var(--accent); }
  .movie-card-img { height: 180px; width: 100%; object-fit: cover; background: #1a1a28; }
  .movie-card-body { padding: 15px; }
  .movie-title { font-size: 15px; font-weight: 700; margin-bottom: 5px; color: #fff; }
  .movie-meta { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
  .rating-badge { color: #f59e0b; font-weight: 700; display: flex; align-items: center; gap: 3px; }
</style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
  <?php include 'includes/topbar.php'; ?>
  <div class="page-content">
    <div class="page-header">
      <div>
        <h1 class="page-title"><?= $page_title ?></h1>
        <p class="page-subtitle"><?= $total ?> movies in this category</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add Movie</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card" style="padding:12px 16px; margin-bottom:20px; display:flex; gap:15px;">
      <?php if(isset($cat_map)): ?>
      <select class="form-control" style="max-width:250px;" onchange="window.location.href='categories.php?type='+this.value">
         <?php foreach($cat_map as $k => $v): ?>
         <option value="<?= $k ?>" <?= (isset($_GET['type']) && $_GET['type'] === $k) ? 'selected' : '' ?>><?= $v['title'] ?></option>
         <?php endforeach; ?>
      </select>
      <?php endif; ?>
      <div class="search-box" style="flex:1;"><i class="fas fa-search"></i><input type="text" id="catSearch" class="form-control" placeholder="Search movies in this category..."></div>
      <div style="display:flex; border:1px solid var(--border); border-radius:8px; overflow:hidden;">
         <button onclick="switchView('grid')" id="btnGridView" class="btn active" style="padding:10px 15px; border:none; border-radius:0; background:rgba(6,182,212,0.15); color:var(--accent);"><i class="fas fa-th-large"></i></button>
         <button onclick="switchView('table')" id="btnTableView" class="btn" style="padding:10px 15px; border:none; border-radius:0; background:transparent; color:var(--text-muted);"><i class="fas fa-list"></i></button>
      </div>
    </div>

    <!-- Grid View -->
    <div id="catGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px;">
    <?php mysqli_data_seek($movies, 0); while($m = mysqli_fetch_assoc($movies)): ?>
    <div class="movie-card">
      <div style="position:relative;">
        <?php $real_img = (strpos($m['image_path'], 'http') === 0 || strpos($m['image_path'], '../') === 0) ? $m['image_path'] : '../assets/images/' . $m['image_path']; ?>
        <img src="<?= htmlspecialchars($real_img) ?>" class="movie-card-img" onerror="this.src='';this.style.background='#1a1a28'">
        <?php if(!empty($m['badge'])): ?>
        <span style="position:absolute;top:10px;right:10px;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:50px;"><?= htmlspecialchars($m['badge']) ?></span>
        <?php endif; ?>
      </div>
      <div class="movie-card-body">
        <h3 class="movie-title"><?= htmlspecialchars($m['title']) ?></h3>
        <div style="font-size:10px; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:1px;">Added on <?= date('d M Y', strtotime($m['created_at'])) ?></div>
        <div class="movie-meta">
           <span class="rating-badge"><i class="fas fa-star"></i> <?= $m['rating'] ?></span>
           <span>•</span>
           <span><?= htmlspecialchars($m['language']) ?></span>
           <span>•</span>
           <span><?= htmlspecialchars($m['duration']) ?></span>
        </div>
        <p style="font-size:12px;color:var(--text-muted);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:15px;"><?= htmlspecialchars($m['description']) ?></p>
        
        <div style="display:flex; gap:6px;">
          <button type="button" class="btn btn-sm" style="flex:1; background:rgba(6,182,212,0.15); color:#06b6d4; border:none;" title="Edit" onclick="openEditModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['title'])) ?>', '<?= htmlspecialchars(addslashes($m['language'])) ?>', '<?= htmlspecialchars(addslashes($m['badge'])) ?>', <?= $m['rating'] ?>, '<?= htmlspecialchars(addslashes($m['duration'])) ?>', '<?= htmlspecialchars(addslashes($m['image_path'])) ?>', '<?= htmlspecialchars(addslashes($m['description'])) ?>')"><i class="fas fa-edit"></i></button>
          
          <button type="button" class="btn btn-sm" style="background:rgba(168,85,247,0.15); color:#a855f7; border:none;" title="Duplicate/Copy" onclick="copyMovie('<?= htmlspecialchars(addslashes($m['title'])) ?>', '<?= htmlspecialchars(addslashes($m['language'])) ?>', '<?= htmlspecialchars(addslashes($m['badge'])) ?>', <?= $m['rating'] ?>, '<?= htmlspecialchars(addslashes($m['duration'])) ?>', '<?= htmlspecialchars(addslashes($m['image_path'])) ?>', '<?= htmlspecialchars(addslashes($m['description'])) ?>')"><i class="fas fa-copy"></i></button>

          <form method="POST" onsubmit="return confirm('Delete this movie?')" style="display:inline;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px;"><i class="fas fa-trash"></i></button>
          </form>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
    </div>

    <!-- Table View (Hidden by default) -->
    <div id="catTableWrap" style="display:none;" class="content-card">
      <div class="table-wrapper">
        <table class="data-table" id="catTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Poster</th>
              <th>Title</th>
              <th>Language</th>
              <th>Badge</th>
              <th>Rating</th>
              <th>Duration</th>
              <th>Added Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php mysqli_data_seek($movies, 0); while($m = mysqli_fetch_assoc($movies)): ?>
            <tr>
              <td><span class="booking-id">#<?= $m['id'] ?></span></td>
              <td>
                <?php $real_img = (strpos($m['image_path'], 'http') === 0 || strpos($m['image_path'], '../') === 0) ? $m['image_path'] : '../assets/images/' . $m['image_path']; ?>
                <img src="<?= htmlspecialchars($real_img) ?>" style="width:40px; height:50px; border-radius:4px; object-fit:cover;" onerror="this.src='';this.style.background='#1a1a28'">
              </td>
              <td style="font-weight:600;"><?= htmlspecialchars($m['title']) ?></td>
              <td><?= htmlspecialchars($m['language']) ?></td>
              <td><span class="badge-seats"><?= htmlspecialchars($m['badge'] ?: '—') ?></span></td>
              <td><span class="status-badge paid"><i class="fas fa-star" style="font-size:10px;"></i> <?= $m['rating'] ?></span></td>
              <td><?= htmlspecialchars($m['duration']) ?></td>
              <td style="font-size:11px;"><?= date('d M Y', strtotime($m['created_at'])) ?></td>
              <td>
                <div style="display:flex; gap:6px;">
                  <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15); color:#06b6d4; border:none;" onclick="openEditModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['title'])) ?>', '<?= htmlspecialchars(addslashes($m['language'])) ?>', '<?= htmlspecialchars(addslashes($m['badge'])) ?>', <?= $m['rating'] ?>, '<?= htmlspecialchars(addslashes($m['duration'])) ?>', '<?= htmlspecialchars(addslashes($m['image_path'])) ?>', '<?= htmlspecialchars(addslashes($m['description'])) ?>')"><i class="fas fa-edit"></i></button>
                  <button type="button" class="btn btn-sm" style="background:rgba(168,85,247,0.15); color:#a855f7; border:none;" onclick="copyMovie('<?= htmlspecialchars(addslashes($m['title'])) ?>', '<?= htmlspecialchars(addslashes($m['language'])) ?>', '<?= htmlspecialchars(addslashes($m['badge'])) ?>', <?= $m['rating'] ?>, '<?= htmlspecialchars(addslashes($m['duration'])) ?>', '<?= htmlspecialchars(addslashes($m['image_path'])) ?>', '<?= htmlspecialchars(addslashes($m['description'])) ?>')"><i class="fas fa-copy"></i></button>
                  <form method="POST" onsubmit="return confirm('Delete this movie?')" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
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

    <?php if($total === 0): ?>
    <div class="empty-state"><i class="fas fa-film"></i><p>No movies added yet.</p></div>
    <?php endif; ?>
  </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Movie to <?= $page_title ?></span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" autocomplete="off" id="addCatForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Language</label>
            <input type="text" name="language" class="form-control" placeholder="e.g. Hindi, English" required>
          </div>
          <div class="form-group">
            <label class="form-label">Badge/Tag</label>
            <input type="text" name="badge" class="form-control" placeholder="e.g. HIT, NEW, 4K">
          </div>
          <div class="form-group">
            <label class="form-label">Rating (0.0 to 5.0)</label>
            <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" value="4.5">
          </div>
          <div class="form-group">
            <label class="form-label">Duration</label>
            <input type="text" name="duration" class="form-control" placeholder="e.g. 2h 30m">
          </div>
          <div class="form-group full">
            <label class="form-label">Poster Image URL/Path</label>
            <input type="text" name="image_path" class="form-control" placeholder="e.g. movie.jpg or https://..." required>
          </div>
          <div class="form-group full">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Movie</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModalOverlay">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Movie</span>
      <button class="modal-close" onclick="closeModal('editModalOverlay')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editCatForm" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="edit_title" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Language</label>
            <input type="text" name="language" id="edit_language" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Badge/Tag</label>
            <input type="text" name="badge" id="edit_badge" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Rating</label>
            <input type="number" name="rating" id="edit_rating" class="form-control" step="0.1" min="0" max="5">
          </div>
          <div class="form-group">
            <label class="form-label">Duration</label>
            <input type="text" name="duration" id="edit_duration" class="form-control">
          </div>
          <div class="form-group full">
            <label class="form-label">Poster Image URL/Path</label>
            <input type="text" name="image_path" id="edit_image_path" class="form-control" required>
          </div>
          <div class="form-group full">
            <label class="form-label">Description</label>
            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModalOverlay')">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Movie</button>
      </div>
    </form>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openEditModal(id, title, lang, badge, rating, dur, img, desc) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_language').value = lang;
    document.getElementById('edit_badge').value = badge;
    document.getElementById('edit_rating').value = rating;
    document.getElementById('edit_duration').value = dur;
    document.getElementById('edit_image_path').value = img;
    document.getElementById('edit_description').value = desc;
    openModal('editModalOverlay');
}

function copyMovie(title, lang, badge, rating, dur, img, desc) {
    const form = document.getElementById('addCatForm');
    form.querySelector('[name="title"]').value = title + ' (Copy)';
    form.querySelector('[name="language"]').value = lang;
    form.querySelector('[name="badge"]').value = badge;
    form.querySelector('[name="rating"]').value = rating;
    form.querySelector('[name="duration"]').value = dur;
    form.querySelector('[name="image_path"]').value = img;
    form.querySelector('[name="description"]').value = desc;
    openModal('addModal');
    showToast('Movie details copied to new form!', 'success');
}

document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('catSearch');
    const grid = document.getElementById('catGrid');
    if (search && grid) {
        const table = document.getElementById('catTable');
        search.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            
            // Filter Grid
            grid.querySelectorAll('.movie-card').forEach(card => {
                const title = card.querySelector('.movie-title');
                card.style.display = (title && title.textContent.toLowerCase().includes(term)) ? '' : 'none';
            });

            // Filter Table
            if(table) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    const titleCol = row.cells[2];
                    row.style.display = (titleCol && titleCol.textContent.toLowerCase().includes(term)) ? '' : 'none';
                });
            }
        });
    }

    const forms = ['addCatForm', 'editCatForm'];
    forms.forEach(fid => {
        const form = document.getElementById(fid);
        if(form) {
            form.addEventListener('submit', function(e) {
                let valid = true;
                this.querySelectorAll('.error-msg').forEach(el => el.remove());
                this.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

                const showError = (el, msg) => {
                    el.style.borderColor = '#ef4444';
                    const parent = el.closest('.form-group');
                    const err = document.createElement('small');
                    err.className = 'error-msg';
                    err.style.color = '#ef4444';
                    err.style.display = 'block';
                    err.style.marginTop = '4px';
                    err.innerText = msg;
                    if(parent) parent.appendChild(err);
                    if(valid) { el.focus(); showToast(msg, 'error'); }
                    valid = false;
                };

                const title = this.querySelector('[name="title"]');
                const lang = this.querySelector('[name="language"]');
                const path = this.querySelector('[name="image_path"]');
                const rating = this.querySelector('[name="rating"]');
                const duration = this.querySelector('[name="duration"]');
                const desc = this.querySelector('[name="description"]');

                if(title.value.trim().length < 2) showError(title, 'Title is required (min 2 chars).');
                if(lang.value.trim().length < 2) showError(lang, 'Language is required.');
                if(path.value.trim().length < 3) showError(path, 'Image URL/path is required.');
                
                const rVal = parseFloat(rating.value);
                if(isNaN(rVal) || rVal < 0 || rVal > 5) showError(rating, 'Rating must be between 0.0 and 5.0');
                
                if(duration.value.trim().length < 2) showError(duration, 'Duration is required.');
                if(desc.value.trim().length < 10) showError(desc, 'Description must be at least 10 characters.');

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

function switchView(view) {
    const grid = document.getElementById('catGrid');
    const table = document.getElementById('catTableWrap');
    const btnGrid = document.getElementById('btnGridView');
    const btnTable = document.getElementById('btnTableView');

    if(view === 'grid') {
        grid.style.display = 'grid';
        table.style.display = 'none';
        btnGrid.classList.add('active');
        btnGrid.style.background = 'rgba(6,182,212,0.15)';
        btnGrid.style.color = 'var(--accent)';
        btnTable.classList.remove('active');
        btnTable.style.background = 'transparent';
        btnTable.style.color = 'var(--text-muted)';
    } else {
        grid.style.display = 'none';
        table.style.display = 'block';
        btnTable.classList.add('active');
        btnTable.style.background = 'rgba(6,182,212,0.15)';
        btnTable.style.color = 'var(--accent)';
        btnGrid.classList.remove('active');
        btnGrid.style.background = 'transparent';
        btnGrid.style.color = 'var(--text-muted)';
    }
}
</script>
</body>
</html>
