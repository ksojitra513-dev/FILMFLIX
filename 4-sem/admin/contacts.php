<?php
require_once 'includes/auth.php';
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        $status = mysqli_real_escape_string($con, $_POST['status'] ?? 'unread');
        
        $sql = "INSERT INTO contact (fullname, email, message, status) VALUES ('$fullname', '$email', '$message', '$status')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'Contact message manually added!'];
        else $msg = ['type'=>'error','text'=>mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM contact WHERE id=$id");
        $msg = ['type'=>'success', 'text'=>'Message deleted permanently.'];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $new_status = $_POST['status'] === 'read' ? 'unread' : 'read';
        mysqli_query($con, "UPDATE contact SET status='$new_status' WHERE id=$id");
        $msg = ['type'=>'success', 'text'=>'Message status updated.'];
    }
}

// Add status column if not exists
try { mysqli_query($con, "ALTER TABLE contact ADD COLUMN status VARCHAR(20) DEFAULT 'unread'"); } catch(Exception $e) {}

$contacts = mysqli_query($con, "SELECT * FROM contact ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Contact Messages</title>
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
      <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
        <div>
          <h1 class="page-title">Contact Inquiries</h1>
          <p class="page-subtitle">Messages sent from the contact us page</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('addContactModal')"><i class="fas fa-plus"></i> Add Inquiry</button>
      </div>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="cSearch" class="form-control" placeholder="Search entries..."></div>
      </div>
      <table class="data-table" id="cTable">
        <thead><tr><th>#</th><th>Sender</th><th>Email</th><th>Status</th><th>Date Received</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $i=1; while($c = mysqli_fetch_assoc($contacts)): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td style="font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($c['fullname']) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:var(--accent-cyan);text-decoration:none;"><?= htmlspecialchars($c['email']) ?></a></td>
          <td><span class="status-badge <?= strtolower($c['status'] ?? 'unread') ?>"><?= ucfirst($c['status'] ?? 'unread') ?></span></td>
          <td><?= date('d M, h:i A', strtotime($c['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:4px;">
              <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Message" onclick="openViewContactModal('<?= htmlspecialchars(addslashes($c['fullname'])) ?>', '<?= htmlspecialchars(addslashes($c['email'])) ?>', '<?= htmlspecialchars(addslashes($c['message'])) ?>', '<?= date('F j, Y, g:i a', strtotime($c['created_at'])) ?>')"><i class="fas fa-eye"></i></button>
              
              <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <input type="hidden" name="status" value="<?= $c['status'] ?? 'unread' ?>">
                <button type="submit" class="btn btn-sm btn-primary" title="Toggle Read Status"><i class="fas fa-check"></i></button>
              </form>

              <button type="button" class="btn btn-sm" style="background:rgba(16,185,129,0.15);color:#10b981;border:none;" title="Quick Reply" onclick="openReplyModal('<?= htmlspecialchars(addslashes($c['fullname'])) ?>', '<?= htmlspecialchars(addslashes($c['email'])) ?>')"><i class="fas fa-reply"></i></button>

              <form method="POST" onsubmit="return confirm('Delete this inquiry?')" style="display:inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
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

<!-- View Message Modal -->
<div class="modal-overlay" id="viewContactModal">
  <div class="modal" style="max-width:550px;">
    <div class="modal-header">
      <span class="modal-title">Inquiry Details</span>
      <button class="modal-close" onclick="closeModal('viewContactModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
       <div style="display:flex; align-items:center; gap:15px; margin-bottom:20px;">
          <div id="vc_avatar" style="width:50px; height:50px; border-radius:12px; background:linear-gradient(135deg,#a855f7,#06b6d4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:20px;"></div>
          <div>
             <h4 id="vc_name" style="color:#fff; font-size:18px; font-weight:700; margin:0;"></h4>
             <p id="vc_email" style="color:var(--accent); font-size:13px; margin:0;"></p>
          </div>
       </div>
       <div class="content-card" style="background:rgba(255,255,255,0.03); border:1px solid var(--border); padding:20px;">
          <label style="display:block; font-size:10px; text-transform:uppercase; color:var(--text-muted); margin-bottom:10px; letter-spacing:1px;">The Message Content</label>
          <div id="vc_msg" style="color:var(--text-secondary); line-height:1.7; font-size:15px; white-space:pre-wrap;"></div>
       </div>
       <div id="vc_date" style="text-align:right; font-size:11px; color:var(--text-muted); margin-top:10px;"></div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewContactModal')">Close Message</button>
    </div>
  </div>
</div>

<!-- Add Contact Modal -->
<div class="modal-overlay" id="addContactModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Add Manual Inquiry</span>
      <button class="modal-close" onclick="closeModal('addContactModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addContactForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid cols-1">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" placeholder="e.g. John Doe" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Their message..." required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
              <option value="unread" selected>Unread</option>
              <option value="read">Read</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addContactModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Inquiry</button>
      </div>
    </form>
  </div>
</div>

<!-- Reply Modal -->
<div class="modal-overlay" id="replyModal">
  <div class="modal" style="max-width:500px;">
    <div class="modal-header">
      <span class="modal-title">Send Response</span>
      <button class="modal-close" onclick="closeModal('replyModal')"><i class="fas fa-times"></i></button>
    </div>
    <form id="replyForm" novalidate>
       <div class="modal-body">
          <div class="form-group">
             <label class="form-label">To:</label>
             <input type="text" id="reply_to" class="form-control" readonly style="opacity:0.7;">
          </div>
          <div class="form-group">
             <label class="form-label">Your Message</label>
             <textarea id="reply_msg" class="form-control" rows="5" placeholder="Compose your reply here..."></textarea>
          </div>
       </div>
       <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeModal('replyModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Send Email <i class="fas fa-paper-plane" style="margin-left:5px;"></i></button>
       </div>
    </form>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
filterTable('cSearch','cTable');

function openViewContactModal(name, email, msg, date) {
    document.getElementById('vc_name').innerText = name;
    document.getElementById('vc_email').innerText = email;
    document.getElementById('vc_msg').innerText = msg;
    document.getElementById('vc_date').innerText = "Received on: " + date;
    document.getElementById('vc_avatar').innerText = name.charAt(0).toUpperCase();
    openModal('viewContactModal');
}

function openReplyModal(name, email) {
    document.getElementById('reply_to').value = `${name} <${email}>`;
    document.getElementById('reply_msg').value = `Hello ${name.split(' ')[0]},\n\nThank you for reaching out to FilmFlix. Regarding your inquiry...`;
    openModal('replyModal');
}

document.addEventListener('DOMContentLoaded', () => {
    const replyForm = document.getElementById('replyForm');
    if(replyForm) {
        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = document.getElementById('reply_msg');
            if(msg.value.trim().length < 10) {
               showToast('Please enter a longer response (min 10 chars)', 'error');
               msg.style.borderColor = '#ef4444';
               return;
            }
            
            // For now, simulate sending by opening mailto
            const nameEmail = document.getElementById('reply_to').value;
            const email = nameEmail.match(/<(.*)>/)[1];
            const subject = "Reply from FilmFlix Support";
            const body = encodeURIComponent(msg.value);
            
            showToast('Opening your email client...', 'success');
            setTimeout(() => {
               window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
               closeModal('replyModal');
            }, 1000);
        });
        
        document.getElementById('reply_msg').addEventListener('input', function() {
           this.style.borderColor = '';
        });
    }

    const addForm = document.getElementById('addContactForm');
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

            const fname = this.querySelector('[name="fullname"]');
            const email = this.querySelector('[name="email"]');
            const msg = this.querySelector('[name="message"]');

            if(fname.value.trim().length < 3) showError(fname, 'Full Name is required (min 3 chars).');
            if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) showError(email, 'A valid email address is required.');
            if(msg.value.trim().length < 5) showError(msg, 'Message is required (min 5 chars).');

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


