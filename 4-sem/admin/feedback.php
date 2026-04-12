<?php
require_once 'includes/auth.php';
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $rating = (int)$_POST['star_rating'];
        $subject = mysqli_real_escape_string($con, $_POST['subject']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        $status = mysqli_real_escape_string($con, $_POST['status'] ?? 'pending');
        
        $sql = "INSERT INTO feedback (star_rating, subject, message, status) VALUES ($rating, '$subject', '$message', '$status')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Feedback successfully added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM feedback WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Feedback deleted.'];
    }
    
    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'approved' ? 'pending' : 'approved';
        mysqli_query($con, "UPDATE feedback SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Feedback status updated!'];
    }
}

// Check for status column
try { mysqli_query($con, "ALTER TABLE feedback ADD COLUMN status VARCHAR(20) DEFAULT 'pending'"); } catch(Exception $e) {}

$feedback = mysqli_query($con, "SELECT * FROM feedback ORDER BY submitted_at DESC");
$avg_rating = mysqli_fetch_assoc(mysqli_query($con, "SELECT AVG(star_rating) as avg_r FROM feedback"))['avg_r'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Feedback</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
<style>
.stars-display i { color: #5f5f72; font-size: 14px; margin-right: 2px; }
.stars-display i.active { color: #f59e0b; }
.fb-card { background:rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:12px; padding:20px; transition:var(--transition); position:relative; }
.fb-card:hover { transform:translateY(-2px); background:rgba(255,255,255,0.04); border-color:var(--border-hover); box-shadow:0 10px 30px rgba(0,0,0,0.5); }
.fb-quote { position:absolute; right:20px; top:20px; font-size:40px; color:rgba(255,255,255,0.05); }
.fb-date { font-size:11px; color:var(--text-muted); margin-bottom:10px; }
.fb-subject { font-size:16px; font-weight:600; color:var(--text-primary); margin:8px 0; }
.fb-msg { font-size:13.5px; color:var(--text-secondary); line-height:1.6; font-style:italic; }
</style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
  <?php include 'includes/topbar.php'; ?>
  <div class="page-content">
    <div class="page-header">
      <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
        <div>
          <h1 class="page-title">User Feedback</h1>
          <p class="page-subtitle">Average Rating: <strong style="color:#f59e0b;font-size:16px;margin:0 5px;"><?= number_format($avg_rating,1) ?></strong> out of 5</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('addFeedbackModal')"><i class="fas fa-plus"></i> Add Feedback</button>
      </div>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <div class="toolbar">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" id="fbSearch" class="form-control" placeholder="Search feedback...">
        </div>
      </div>
      <div class="table-wrapper">
        <table class="data-table" id="fbTable">
          <thead>
            <tr>
              <th>#</th>
              <th>ID</th>
              <th>Rating</th>
              <th>Subject</th>
              <th>Message</th>
              <th>Submitted At</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php $i=1; while($f = mysqli_fetch_assoc($feedback)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><span style="font-size:11px; color:var(--text-muted);">#<?= $f['id'] ?></span></td>
              <td>
                <div class="stars-display">
                  <?php for($j=1;$j<=5;$j++): ?>
                    <i class="fas fa-star <?= $j<=$f['star_rating']?'active':'' ?>"></i>
                  <?php endfor; ?>
                </div>
              </td>
              <td style="font-weight:600; color:#fff;"><?= htmlspecialchars($f['subject']) ?></td>
              <td style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--text-secondary); font-size:12.5px;">
                <?= htmlspecialchars($f['message']) ?>
              </td>
              <td><?= date('d M, h:i A', strtotime($f['submitted_at'])) ?></td>
              <td><span class="status-badge <?= strtolower($f['status'] ?? 'pending') ?>"><?= ucfirst($f['status'] ?? 'pending') ?></span></td>
              <td>
                <div style="display:flex; gap:4px;">
                  <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Full Feedback" onclick="openViewFeedbackModal(<?= $f['star_rating'] ?>, '<?= htmlspecialchars(addslashes($f['subject'])) ?>', '<?= htmlspecialchars(addslashes($f['message'])) ?>', '<?= date('F j, Y, h:i a', strtotime($f['submitted_at'])) ?>')"><i class="fas fa-eye"></i></button>
                  
                  <form method="POST" style="display:inline;">
                     <input type="hidden" name="action" value="update_status">
                     <input type="hidden" name="id" value="<?= $f['id'] ?>">
                     <input type="hidden" name="status" value="<?= $f['status'] ?? 'pending' ?>">
                     <button type="submit" class="btn btn-sm btn-primary" title="Toggle Approval Status"><i class="fas fa-check"></i></button>
                  </form>

                  <form method="POST" onsubmit="return confirm('Delete this feedback entry?')" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $f['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" title="DeletePermanently"><i class="fas fa-trash"></i></button>
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

<!-- Add Feedback Modal -->
<div class="modal-overlay" id="addFeedbackModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Feedback</span>
      <button class="modal-close" onclick="closeModal('addFeedbackModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addFeedbackForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="e.g. Great App!" required>
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Feedback message..." required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Star Rating (1-5)</label>
            <input type="number" name="star_rating" class="form-control" min="1" max="5" required>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
              <option value="approved">Approved</option>
              <option value="pending" selected>Pending</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addFeedbackModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Feedback</button>
      </div>
    </form>
  </div>
</div>

<!-- View Review Modal -->
<div class="modal-overlay" id="viewFeedbackModal">
  <div class="modal" style="max-width:550px;">
    <div class="modal-header">
      <span class="modal-title">Review Details</span>
      <button class="modal-close" onclick="closeModal('viewFeedbackModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="padding:30px;">
       <div id="view_star_container" style="font-size:32px; color:#f59e0b; text-align:center; margin-bottom:20px; filter:drop-shadow(0 0 8px rgba(245,158,11,0.4));"></div>
       <div style="text-align:center; margin-bottom:25px;">
          <h2 id="view_subject" style="font-size:24px; font-weight:800; color:#fff; margin-bottom:5px;"></h2>
          <p id="view_date" style="font-size:12px; color:var(--text-muted);"></p>
       </div>
       <div class="content-card" style="background:rgba(255,255,255,0.03); border:1px solid var(--border); padding:25px; border-radius:16px;">
          <i class="fas fa-quote-left" style="font-size:20px; color:rgba(255,255,255,0.1); display:block; margin-bottom:15px;"></i>
          <p id="view_msg" style="color:var(--text-secondary); font-size:16px; line-height:1.7; font-style:italic; margin:0; text-align:center;"></p>
          <i class="fas fa-quote-right" style="font-size:20px; color:rgba(255,255,255,0.1); display:block; margin-top:15px; text-align:right;"></i>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewFeedbackModal')">Done</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
filterTable('fbSearch','fbTable');

function openViewFeedbackModal(stars, subject, msg, date) {
    document.getElementById('view_subject').innerText = subject;
    document.getElementById('view_msg').innerText = msg;
    document.getElementById('view_date').innerText = "Submitted on: " + date;
    
    let starHtml = '';
    for(let i=1; i<=5; i++) {
        starHtml += `<i class="fas fa-star ${i<=stars ? 'active' : ''}" style="color:${i<=stars ? '#f59e0b' : '#333'}"></i> `;
    }
    document.getElementById('view_star_container').innerHTML = starHtml;
    openModal('viewFeedbackModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const addForm = document.getElementById('addFeedbackForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            let valid = true;
            this.querySelectorAll('.error-msg').forEach(el => el.remove());
            this.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

            const showError = (el, msg) => {
                el.style.borderColor = '#ef4444';
                const parent = el.closest('.form-group');
                const err = document.createElement('small');
                err.className = 'error-msg';
                err.innerText = msg;
                if(parent) parent.appendChild(err);
                if(valid) { el.focus(); showToast(msg, 'error'); }
                valid = false;
            };

            const subj = this.querySelector('[name="subject"]');
            const msg = this.querySelector('[name="message"]');
            const score = this.querySelector('[name="star_rating"]');

            if(subj.value.trim().length < 2) showError(subj, 'Subject is required.');
            if(msg.value.trim().length < 3) showError(msg, 'Message is required.');
            if(!score.value || score.value < 1 || score.value > 5) showError(score, 'Rating must be between 1 and 5.');

            if(!valid) e.preventDefault();
        });

        addForm.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const err = this.parentNode.querySelector('.error-msg');
                if(err) err.remove();
            });
        });
    }
});
</script>
</body>
</html>


