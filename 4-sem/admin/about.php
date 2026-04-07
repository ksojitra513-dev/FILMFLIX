<?php
require_once 'includes/auth.php';
require_once '../config.php';
$msg = '';

// Handle About Content Form Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $subtitle = mysqli_real_escape_string($con, $_POST['hero_subtitle']);
    $wc_title = mysqli_real_escape_string($con, $_POST['about_welcome_title']);
    $main_title = mysqli_real_escape_string($con, $_POST['about_main_title']);
    $desc = mysqli_real_escape_string($con, $_POST['about_full_desc']);
    $exp_val = (int)$_POST['user_exp_val'];
    $sec_pay = (int)$_POST['secure_pay_val'];
    $stats_m = mysqli_real_escape_string($con, $_POST['stats_movies']);
    $stats_u = mysqli_real_escape_string($con, $_POST['stats_users']);
    
    // Handle optional image upload
    $img_query = "";
    if (!empty($_FILES['main_image']['name'])) {
        $img = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['main_image']['name']);
        if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], '../' . $img)) {
            $img_query = "`main_image`='$img',";
        }
    }
    
    // Update content (assuming single row id=1)
    $sql = "UPDATE about_content SET 
            `hero_subtitle`='$subtitle', 
            $img_query 
            `about_welcome_title`='$wc_title', 
            `about_main_title`='$main_title', 
            `about_full_desc`='$desc', 
            `user_exp_val`=$exp_val, 
            `secure_pay_val`=$sec_pay, 
            `stats_movies`='$stats_m', 
            `stats_users`='$stats_u' 
            WHERE id=1";
            
    if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'About content updated successfully!'];
    else $msg = ['type'=>'error','text'=>'Error updating content: '.mysqli_error($con)];
}

// Handle About Cards
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card'])) {
    $type = mysqli_real_escape_string($con, $_POST['card_type']);
    $icon = mysqli_real_escape_string($con, $_POST['icon_class']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    
    if(mysqli_query($con, "INSERT INTO about_cards (card_type, icon_class, title, description) VALUES ('$type','$icon','$title','$desc')"))
        $msg = ['type'=>'success','text'=>'Card added!'];
}

try {
    @mysqli_query($con, "ALTER TABLE about_cards ADD COLUMN status VARCHAR(20) DEFAULT 'active'");
} catch (Exception $e) {
    // Column likely already exists, ignore
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_card'])) {
    $id = (int)$_POST['id'];
    mysqli_query($con, "DELETE FROM about_cards WHERE id=$id");
    $msg = ['type'=>'success','text'=>'Card deleted.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_card'])) {
    $id = (int)$_POST['id'];
    $type = mysqli_real_escape_string($con, $_POST['card_type']);
    $icon = mysqli_real_escape_string($con, $_POST['icon_class']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    mysqli_query($con, "UPDATE about_cards SET card_type='$type', icon_class='$icon', title='$title', description='$desc' WHERE id=$id");
    $msg = ['type'=>'success','text'=>'Card updated successfully!'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $id = (int)$_POST['id'];
    $status = mysqli_real_escape_string($con, $_POST['status']);
    mysqli_query($con, "UPDATE about_cards SET status='$status' WHERE id=$id");
    $msg = ['type'=>'success','text'=>"Card status updated to $status."];
}

// Fetch current data
$content = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM about_content WHERE id=1"));
$cards = mysqli_query($con, "SELECT * FROM about_cards ORDER BY card_type, id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — About Content</title>
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
        <h1 class="page-title">About Us Content</h1>
        <p class="page-subtitle">Manage website text, stats, and feature cards</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addCardModal')"><i class="fas fa-plus"></i> Add Feature Card</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <!-- Main Content Form -->
    <div class="content-card">
      <h3 class="card-title"><i class="fas fa-edit"></i> Edit Main Page Content</h3>
      <form method="POST" enctype="multipart/form-data" id="mainContentForm" novalidate>
        <input type="hidden" name="update_content" value="1">
        <div class="form-grid">
           <!-- Hero Section -->
           <div class="form-group full" style="margin-top:10px; border-bottom:1px solid var(--border); padding-bottom:10px;">
             <strong>Hero Section</strong>
           </div>
           <div class="form-group full">
             <label class="form-label">Hero Subtitle</label>
             <input type="text" name="hero_subtitle" class="form-control" value="<?= htmlspecialchars($content['hero_subtitle']??'') ?>">
           </div>
           
           <!-- About Section -->
           <div class="form-group full" style="margin-top:10px; border-bottom:1px solid var(--border); padding-bottom:10px;">
             <strong>About Section</strong>
           </div>
           <div class="form-group">
             <label class="form-label">Welcome Title (Small)</label>
             <input type="text" name="about_welcome_title" class="form-control" value="<?= htmlspecialchars($content['about_welcome_title']??'') ?>">
           </div>
           <div class="form-group">
             <label class="form-label">Main Heading</label>
             <input type="text" name="about_main_title" class="form-control" value="<?= htmlspecialchars($content['about_main_title']??'') ?>">
           </div>
           <div class="form-group full">
             <label class="form-label">Full Description</label>
             <textarea name="about_full_desc" class="form-control" rows="4"><?= htmlspecialchars($content['about_full_desc']??'') ?></textarea>
           </div>
           <div class="form-group">
             <label class="form-label">Side Image / Main Poster</label>
             <input type="file" name="main_image" class="form-control" accept="image/*">
             <?php if(!empty($content['main_image'])): ?>
               <small style="color:var(--text-muted);display:block;margin-top:5px;">Current: <?= htmlspecialchars($content['main_image']) ?></small>
             <?php endif; ?>
           </div>
           
           <!-- Progress Bars / Stats -->
           <div class="form-group full" style="margin-top:10px; border-bottom:1px solid var(--border); padding-bottom:10px;">
             <strong>Statistics & Progress Bars</strong>
           </div>
           <div class="form-group">
             <label class="form-label">User Experience (%)</label>
             <input type="number" name="user_exp_val" class="form-control" value="<?= htmlspecialchars($content['user_exp_val']??95) ?>" min="0" max="100">
           </div>
           <div class="form-group">
             <label class="form-label">Secure Payment (%)</label>
             <input type="number" name="secure_pay_val" class="form-control" value="<?= htmlspecialchars($content['secure_pay_val']??100) ?>" min="0" max="100">
           </div>
           <div class="form-group">
             <label class="form-label">Movies Available Text (e.g. 500+)</label>
             <input type="text" name="stats_movies" class="form-control" value="<?= htmlspecialchars($content['stats_movies']??'500+') ?>">
           </div>
           <div class="form-group">
             <label class="form-label">Users Registered Text (e.g. 10k+)</label>
             <input type="text" name="stats_users" class="form-control" value="<?= htmlspecialchars($content['stats_users']??'10k+') ?>">
           </div>
           
           <div class="form-group full" style="margin-top:20px;">
              <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;"><i class="fas fa-save"></i> Save Global Content</button>
           </div>
        </div>
      </form>
    </div>

    <!-- Cards Section -->
    <h3 class="card-title" style="margin-top:40px;"><i class="fas fa-th-large"></i> Feature & Info Cards</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
      <?php while($c = mysqli_fetch_assoc($cards)): ?>
      <div class="content-card" style="margin-bottom:0;position:relative;">
        <span class="status-badge <?= $c['card_type']==='feature'?'active':'pending' ?>" style="position:absolute;top:15px;right:15px;"><?= ucfirst($c['card_type']) ?></span>
        
        <div style="font-size:32px;color:var(--accent);margin-bottom:15px;">
           <?php 
           // Display emoji if info, icon if feature
           if($c['card_type']==='info' && mb_strlen($c['icon_class'],'UTF-8')<=2) { echo $c['icon_class']; }
           else { echo "<i class='{$c['icon_class']}'></i>"; }
           ?>
        </div>
        <h4 style="font-size:16px;font-weight:700;margin-bottom:8px;"><?= htmlspecialchars($c['title']) ?></h4>
        <p style="font-size:13px;color:var(--text-muted);line-height:1.5;margin-bottom:15px;">
           <?= htmlspecialchars($c['description']) ?>
        </p>
        <?php $card_status = $c['status'] ?? 'active'; ?>
        <div style="display:flex; gap:6px; margin-top:8px;">
           <button type="button" class="btn btn-sm" style="flex:1;background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Details" onclick="openViewCardModal('<?= htmlspecialchars(addslashes($c['title'])) ?>', '<?= htmlspecialchars(addslashes($c['description'])) ?>', '<?= $c['card_type'] ?>', '<?= htmlspecialchars(addslashes($c['icon_class'])) ?>')"><i class="fas fa-eye"></i></button>
           <button type="button" class="btn btn-sm btn-primary" style="flex:1;" title="Edit" onclick="openEditCardModal(<?= $c['id'] ?>, '<?= $c['card_type'] ?>', '<?= htmlspecialchars(addslashes($c['icon_class'])) ?>', '<?= htmlspecialchars(addslashes($c['title'])) ?>', '<?= htmlspecialchars(addslashes($c['description'])) ?>')"><i class="fas fa-edit"></i></button>
           
           <form method="POST" style="flex:1;display:flex;" onsubmit="return confirm('Change status of this card?');">
              <input type="hidden" name="toggle_status" value="1">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <input type="hidden" name="status" value="<?= $card_status === 'active' ? 'inactive' : 'active' ?>">
              <button type="submit" class="btn btn-sm" style="width:100%; <?= $card_status==='active' ? 'background:rgba(245,158,11,0.15);color:#f59e0b;' : 'background:rgba(16,185,129,0.15);color:#10b981;' ?>border:none;" title="<?= $card_status==='active' ? 'Deactivate' : 'Activate' ?>"><i class="fas <?= $card_status==='active' ? 'fa-ban' : 'fa-check' ?>"></i></button>
           </form>

           <form method="POST" onsubmit="return confirm('Delete this card?')" style="flex:1;display:flex;">
              <input type="hidden" name="delete_card" value="1">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm" style="width:100%" title="Delete"><i class="fas fa-trash"></i></button>
           </form>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

  </div>
</div>

<!-- Add Card Modal -->
<div class="modal-overlay" id="addCardModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Feature/Info Card</span>
      <button class="modal-close" onclick="closeModal('addCardModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addCardForm" novalidate>
      <input type="hidden" name="add_card" value="1">
      <div class="modal-body">
         <div class="form-grid cols-1">
            <div class="form-group">
               <label class="form-label">Card Type</label>
               <select name="card_type" class="form-control">
                  <option value="feature">Feature (FontAwesome Icon)</option>
                  <option value="info">Info (Emoji Icon)</option>
               </select>
            </div>
            <div class="form-group">
               <label class="form-label">Icon / Emoji</label>
               <input type="text" name="icon_class" class="form-control" placeholder="e.g. fas fa-bolt OR 🎬" required>
               <small style="color:var(--text-muted);display:block;margin-top:4px;">Use full classes like 'fas fa-star' for features, or simple emojis '🎬' for info cards.</small>
            </div>
            <div class="form-group">
               <label class="form-label">Title</label>
               <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
               <label class="form-label">Description</label>
               <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addCardModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Card</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Card Modal -->
<div class="modal-overlay" id="editCardModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Card</span>
      <button class="modal-close" onclick="closeModal('editCardModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editCardForm" novalidate>
      <input type="hidden" name="update_card" value="1">
      <input type="hidden" name="id" id="edit_card_id">
      <div class="modal-body">
         <div class="form-grid cols-1">
            <div class="form-group">
               <label class="form-label">Card Type</label>
               <select name="card_type" id="edit_card_type" class="form-control">
                  <option value="feature">Feature (FontAwesome Icon)</option>
                  <option value="info">Info (Emoji Icon)</option>
               </select>
            </div>
            <div class="form-group">
               <label class="form-label">Icon / Emoji</label>
               <input type="text" name="icon_class" id="edit_icon_class" class="form-control" required>
            </div>
            <div class="form-group">
               <label class="form-label">Title</label>
               <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>
            <div class="form-group">
               <label class="form-label">Description</label>
               <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
            </div>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editCardModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="viewCardModal">
  <div class="modal" style="max-width:550px;">
    <div class="modal-header">
      <span class="modal-title" id="view_card_type_badge" style="font-size:12px; text-transform:uppercase; background:rgba(168,85,247,0.2); color:#a855f7; padding:4px 10px; border-radius:50px; margin-right:10px;">FEATURE</span>
      <span class="modal-title">Card Details</span>
      <button class="modal-close" onclick="closeModal('viewCardModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="text-align:center; padding:40px 20px;">
        <div id="view_card_icon_container" style="font-size:64px; color:var(--accent); margin-bottom:25px; filter:drop-shadow(0 10px 15px rgba(6,182,212,0.3));"></div>
        <h3 id="view_card_title" style="font-size:26px; font-weight:800; margin-bottom:15px; background:linear-gradient(135deg,#fff,#94a3b8); -webkit-background-clip:text; -webkit-text-fill-color:transparent;"></h3>
        <p id="view_card_desc" style="color:var(--text-secondary); line-height:1.8; font-size:16px; max-width:400px; margin:0 auto;"></p>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewCardModal')">Done</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
/* ── Modal helpers ─────────────────────────────── */
function openViewCardModal(title, desc, type, icon) {
    document.getElementById('view_card_title').innerText = title;
    document.getElementById('view_card_desc').innerText = desc;
    document.getElementById('view_card_type_badge').innerText = type;
    const ic = document.getElementById('view_card_icon_container');
    ic.innerHTML = (type === 'info' && icon.length <= 2) ? icon : `<i class="${icon}"></i>`;
    openModal('viewCardModal');
}
function openEditCardModal(id, type, icon, title, desc) {
    document.getElementById('edit_card_id').value      = id;
    document.getElementById('edit_card_type').value    = type;
    document.getElementById('edit_icon_class').value   = icon;
    document.getElementById('edit_title').value        = title;
    document.getElementById('edit_description').value  = desc;
    openModal('editCardModal');
}

/* ── Inject animation CSS once ─────────────────── */
(function() {
    if (document.getElementById('aboutValCSS')) return;
    const s = document.createElement('style');
    s.id = 'aboutValCSS';
    s.innerHTML = `
        @keyframes vShake {
            0%,100%{transform:translateX(0)}
            20%{transform:translateX(-5px)}
            40%{transform:translateX(5px)}
            60%{transform:translateX(-4px)}
            80%{transform:translateX(4px)}
        }
        @keyframes vFade {
            from{opacity:0;transform:translateY(-4px)}
            to{opacity:1;transform:translateY(0)}
        }
        .v-err {
            display:flex; align-items:center; gap:4px;
            color:#ef4444; font-size:11.5px; font-weight:600;
            margin-top:5px; animation:vFade .2s ease;
        }
        .char-cnt {
            font-size:11px; text-align:right; margin-top:3px;
            color:var(--text-muted); transition:color .2s;
        }
        .char-cnt.warn  { color:#f59e0b; }
        .char-cnt.over  { color:#ef4444; font-weight:700; }
    `;
    document.head.appendChild(s);
})();

/* ── Validation utilities ───────────────────────── */
function vErr(input, msg) {
    input.style.cssText += ';border-color:#ef4444!important;box-shadow:0 0 0 3px rgba(239,68,68,.15)!important;';
    input.style.animation = 'none';
    requestAnimationFrame(() => { input.style.animation = 'vShake .3s ease'; });
    const old = input.parentNode.querySelector('.v-err');
    if (old) old.remove();
    const el = document.createElement('small');
    el.className = 'v-err';
    el.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${msg}`;
    input.parentNode.appendChild(el);
}
function vOk(input) {
    input.style.cssText += ';border-color:#10b981!important;box-shadow:0 0 0 3px rgba(16,185,129,.12)!important;';
    const old = input.parentNode.querySelector('.v-err');
    if (old) old.remove();
}
function vClear(input) {
    input.style.borderColor = '';
    input.style.boxShadow   = '';
    const old = input.parentNode.querySelector('.v-err');
    if (old) old.remove();
}
function vClearForm(form) {
    form.querySelectorAll('.v-err').forEach(e => e.remove());
    form.querySelectorAll('.form-control').forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });
}

function addCharCounter(textarea, max) {
    if (!textarea || textarea.dataset.counted) return;
    textarea.dataset.counted = '1';
    const cnt = document.createElement('div');
    cnt.className = 'char-cnt';
    cnt.textContent = `0 / ${max}`;
    textarea.parentNode.appendChild(cnt);
    textarea.addEventListener('input', () => {
        const n = textarea.value.length;
        cnt.textContent = `${n} / ${max}`;
        cnt.className = 'char-cnt' + (n >= max ? ' over' : n >= max * .85 ? ' warn' : '');
    });
}

/* ══════════════════════════════════════════════════
   MAIN CONTENT FORM
══════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    const mainForm = document.getElementById('mainContentForm');
    if (mainForm) {

        addCharCounter(mainForm.querySelector('[name="about_full_desc"]'), 500);

        mainForm.addEventListener('submit', function(e) {
            vClearForm(mainForm);
            let ok = true, first = null;

            const req = [
                { n:'hero_subtitle',       lbl:'Hero Subtitle',         min:5  },
                { n:'about_welcome_title', lbl:'Welcome Title',         min:3  },
                { n:'about_main_title',    lbl:'Main Heading',          min:3  },
                { n:'about_full_desc',     lbl:'Full Description',      min:20 },
                { n:'stats_movies',        lbl:'Movies Available Text', min:1  },
                { n:'stats_users',         lbl:'Users Registered Text', min:1  },
            ];

            req.forEach(({ n, lbl, min }) => {
                const el = mainForm.querySelector(`[name="${n}"]`);
                if (!el) return;
                const v = el.value.trim();
                if (!v) {
                    vErr(el, `${lbl} is required.`);
                    ok = false; if (!first) first = el;
                } else if (v.length < min) {
                    vErr(el, `${lbl} must be at least ${min} characters.`);
                    ok = false; if (!first) first = el;
                } else { vOk(el); }
            });

            ['user_exp_val','secure_pay_val'].forEach(n => {
                const el = mainForm.querySelector(`[name="${n}"]`);
                if (!el) return;
                const v = parseInt(el.value);
                if (isNaN(v) || v < 0 || v > 100) {
                    vErr(el, 'Enter a valid percentage (0–100).'); ok = false; if (!first) first = el;
                } else { vOk(el); }
            });

            const img = mainForm.querySelector('[name="main_image"]');
            if (img && img.files.length) {
                const f = img.files[0];
                if (!['image/jpeg','image/png','image/webp','image/jpg'].includes(f.type)) {
                    vErr(img, 'Upload JPEG, PNG or WEBP only.'); ok = false; if (!first) first = img;
                } else if (f.size > 2*1024*1024) {
                    vErr(img, 'File too large — max 2 MB.'); ok = false; if (!first) first = img;
                }
            }

            if (!ok) {
                e.preventDefault();
                if (first) first.focus();
                const n = mainForm.querySelectorAll('.v-err').length;
                showToast(`Fix ${n} error${n>1?'s':''} before saving.`, 'error');
            }
        });

        mainForm.querySelectorAll('.form-control').forEach(i => {
            i.addEventListener('blur',  function() { if (this.value.trim()) vOk(this); });
            i.addEventListener('input', function() { vClear(this); });
        });
    }

    /* ══════════════════════════════════════════════
       ADD / EDIT CARD FORMS
    ══════════════════════════════════════════════ */
    [
        document.getElementById('addCardForm'),
        document.getElementById('editCardForm')
    ].forEach(form => {
        if (!form) return;

        addCharCounter(form.querySelector('[name="description"]'), 300);

        form.addEventListener('submit', function(e) {
            vClearForm(form);
            let ok = true, first = null, errs = 0;

            const title  = form.querySelector('[name="title"]');
            const desc   = form.querySelector('[name="description"]');
            const icon   = form.querySelector('[name="icon_class"]');
            const typeEl = form.querySelector('[name="card_type"]');
            const type   = typeEl ? typeEl.value : 'feature';

            /* Title */
            if (!title.value.trim()) {
                vErr(title, 'Card title is required.');
                ok=false; errs++; if(!first) first=title;
            } else if (title.value.trim().length < 3) {
                vErr(title, 'Title must be at least 3 characters.');
                ok=false; errs++; if(!first) first=title;
            } else if (title.value.trim().length > 80) {
                vErr(title, 'Title cannot exceed 80 characters.');
                ok=false; errs++; if(!first) first=title;
            } else { vOk(title); }

            /* Description */
            if (!desc.value.trim()) {
                vErr(desc, 'Description is required.');
                ok=false; errs++; if(!first) first=desc;
            } else if (desc.value.trim().length < 10) {
                vErr(desc, 'Description must be at least 10 characters.');
                ok=false; errs++; if(!first) first=desc;
            } else if (desc.value.trim().length > 300) {
                vErr(desc, 'Description cannot exceed 300 characters.');
                ok=false; errs++; if(!first) first=desc;
            } else { vOk(desc); }

            /* Icon / Emoji */
            if (!icon.value.trim()) {
                vErr(icon, 'Icon or Emoji is required.');
                ok=false; errs++; if(!first) first=icon;
            } else if (type === 'feature' && !icon.value.trim().includes('fa-')) {
                vErr(icon, 'Feature cards need a FontAwesome class (e.g. fas fa-bolt).');
                ok=false; errs++; if(!first) first=icon;
            } else if (type === 'info' && icon.value.trim().includes('fa-')) {
                vErr(icon, 'Info cards use emojis, not icon classes (e.g. 🎬).');
                ok=false; errs++; if(!first) first=icon;
            } else { vOk(icon); }

            if (!ok) {
                e.preventDefault();
                if (first) first.focus();
                showToast(errs === 1 ? 'Fix 1 error before saving.' : `Fix ${errs} errors before saving.`, 'error');
            }
        });

        /* Real-time blur feedback */
        form.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) return;
                const type = (form.querySelector('[name="card_type"]') || {}).value;
                if (this.name === 'title') {
                    if (this.value.trim().length < 3)        vErr(this, 'Title must be at least 3 characters.');
                    else if (this.value.trim().length > 80)  vErr(this, 'Title cannot exceed 80 characters.');
                    else vOk(this);
                }
                if (this.name === 'description') {
                    if (this.value.trim().length < 10)        vErr(this, 'Description must be at least 10 characters.');
                    else if (this.value.trim().length > 300)  vErr(this, 'Description cannot exceed 300 characters.');
                    else vOk(this);
                }
                if (this.name === 'icon_class') {
                    if (type === 'feature' && !this.value.includes('fa-'))
                        vErr(this, 'Feature cards need FontAwesome class (e.g. fas fa-bolt).');
                    else if (type === 'info' && this.value.includes('fa-'))
                        vErr(this, 'Info cards use emojis (e.g. 🎬), not icon classes.');
                    else vOk(this);
                }
            });
            input.addEventListener('input', function() { vClear(this); });
        });

        /* Card-type change → update icon placeholder */
        const ts = form.querySelector('[name="card_type"]');
        if (ts) {
            ts.addEventListener('change', function() {
                const ic = form.querySelector('[name="icon_class"]');
                const isInfo = this.value === 'info';
                ic.placeholder = isInfo ? 'e.g. 🎬  (paste any emoji)' : 'e.g. fas fa-bolt  (FontAwesome class)';
                vClear(ic);
                showToast(isInfo ? '📌 Info mode: use Emojis like 🎬 🔒 📺' : '📌 Feature mode: use FontAwesome like fas fa-star', 'success');
            });
        }
    });
});
</script>
</body>
</html>



