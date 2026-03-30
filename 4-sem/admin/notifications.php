<?php
session_start();
require_once '../config.php';
$msg = '';

// Ensure notifications table has proper indexes
try { mysqli_query($con, "ALTER TABLE notifications ADD PRIMARY KEY (id)"); } catch(Exception $e) {}
try { mysqli_query($con, "ALTER TABLE notifications MODIFY id INT NOT NULL AUTO_INCREMENT"); } catch(Exception $e) {}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $user_id    = (int)$_POST['user_id'];
        $booking_id = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : 'NULL';
        $title      = mysqli_real_escape_string($con, $_POST['title']);
        $message    = mysqli_real_escape_string($con, $_POST['message']);
        $type       = mysqli_real_escape_string($con, $_POST['type']);

        $booking_val = $booking_id === 'NULL' ? 'NULL' : $booking_id;
        mysqli_query($con, "INSERT INTO notifications (user_id, booking_id, title, message, type) VALUES ($user_id, $booking_val, '$title', '$message', '$type')");
        $msg = ['type'=>'success', 'text'=>'Notification sent successfully!'];
    }

    if ($action === 'send_all') {
        $title   = mysqli_real_escape_string($con, $_POST['title']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        $type    = mysqli_real_escape_string($con, $_POST['type']);

        $users = mysqli_query($con, "SELECT id FROM user");
        $count = 0;
        while ($u = mysqli_fetch_assoc($users)) {
            mysqli_query($con, "INSERT INTO notifications (user_id, booking_id, title, message, type) VALUES ({$u['id']}, NULL, '$title', '$message', '$type')");
            $count++;
        }
        $msg = ['type'=>'success', 'text'=>"Notification broadcasted to $count users!"];
    }

    if ($action === 'toggle_read') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "UPDATE notifications SET is_read = NOT is_read WHERE id=$id");
        $msg = ['type'=>'success', 'text'=>'Read status toggled.'];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM notifications WHERE id=$id");
        $msg = ['type'=>'success', 'text'=>'Notification deleted.'];
    }

    if ($action === 'mark_all_read') {
        mysqli_query($con, "UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        $msg = ['type'=>'success', 'text'=>'All notifications marked as read.'];
    }

    if ($action === 'delete_read') {
        mysqli_query($con, "DELETE FROM notifications WHERE is_read = 1");
        $msg = ['type'=>'success', 'text'=>'All read notifications deleted.'];
    }
}

// Fetch stats
$total      = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM notifications"))['c'] ?? 0;
$unread     = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM notifications WHERE is_read=0"))['c'] ?? 0;
$today      = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM notifications WHERE DATE(created_at) = CURDATE()"))['c'] ?? 0;
$type_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(DISTINCT type) as c FROM notifications"))['c'] ?? 0;

// Fetch all notifications with user info
$notifications = mysqli_query($con, "
    SELECT n.*, u.fullname as user_name, u.email as user_email
    FROM notifications n 
    LEFT JOIN user u ON n.user_id = u.id 
    ORDER BY n.created_at DESC
");

// Fetch users for the send notification dropdown
$users = mysqli_query($con, "SELECT id, fullname, email FROM user ORDER BY fullname ASC");
$user_list = [];
while ($u = mysqli_fetch_assoc($users)) { $user_list[] = $u; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Notifications</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
<style>
  /* Notification Type Badges */
  .type-badge {
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .type-badge i { font-size: 9px; }
  .type-badge.booking   { background: rgba(168,85,247,0.12); border: 1px solid rgba(168,85,247,0.3); color: #c084fc; }
  .type-badge.payment   { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: #10b981; }
  .type-badge.promotion { background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.3); color: #f59e0b; }
  .type-badge.alert     { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.3);  color: #ef4444; }
  .type-badge.system    { background: rgba(6,182,212,0.12);  border: 1px solid rgba(6,182,212,0.3);  color: #06b6d4; }

  /* Read/Unread indicator */
  .read-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
  }
  .read-dot.unread {
    background: #a855f7;
    box-shadow: 0 0 8px rgba(168,85,247,0.5);
    animation: dotPulse 2s infinite;
  }
  .read-dot.read { background: rgba(255,255,255,0.12); }

  @keyframes dotPulse {
    0%, 100% { box-shadow: 0 0 4px rgba(168,85,247,0.4); }
    50% { box-shadow: 0 0 12px rgba(168,85,247,0.7); }
  }

  /* Message preview in table */
  .msg-preview {
    max-width: 280px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--text-muted);
    font-size: 13px;
  }

  /* Notification title in table */
  .notif-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
    font-size: 14px;
  }

  /* Filter tabs */
  .filter-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .filter-tab {
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-secondary);
    transition: var(--transition);
  }
  .filter-tab:hover { background: var(--bg-card-hover); color: var(--text-primary); }
  .filter-tab.active {
    background: linear-gradient(135deg, rgba(168,85,247,0.2), rgba(6,182,212,0.1));
    border-color: rgba(168,85,247,0.4);
    color: #fff;
  }

  /* User cell */
  .user-cell {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .user-cell-avatar {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #a855f7, #06b6d4);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: #fff;
    flex-shrink: 0;
  }
  .user-cell-name { font-weight: 600; color: var(--text-primary); font-size: 13px; }
  .user-cell-email { font-size: 11px; color: var(--text-muted); }

  /* Bulk action buttons */
  .bulk-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
  }

  /* Recipient toggle */
  .recipient-toggle {
    display: flex;
    border-radius: var(--radius-sm);
    overflow: hidden;
    border: 1px solid var(--border);
    margin-bottom: 16px;
  }
  .recipient-toggle-btn {
    flex: 1;
    padding: 10px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    background: var(--bg-card);
    color: var(--text-secondary);
    border: none;
    transition: var(--transition);
  }
  .recipient-toggle-btn.active {
    background: linear-gradient(135deg, #a855f7, #06b6d4);
    color: #fff;
  }
</style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
  <?php include 'includes/topbar.php'; ?>
  <div class="page-content">
    <div class="page-header">
      <div>
        <h1 class="page-title">Notifications</h1>
        <p class="page-subtitle">Manage and broadcast user notifications</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary" onclick="openModal('sendModal')"><i class="fas fa-paper-plane"></i> Send Notification</button>
      </div>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(168,85,247,0.1);"><i class="fas fa-bell" style="color:#a855f7;"></i></div>
        <div class="stat-info">
          <span class="stat-label">Total</span>
          <span class="stat-value"><?= $total ?></span>
          <span class="stat-change">All notifications</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#ef4444;">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);"><i class="fas fa-envelope" style="color:#ef4444;"></i></div>
        <div class="stat-info">
          <span class="stat-label">Unread</span>
          <span class="stat-value"><?= $unread ?></span>
          <span class="stat-change">Pending attention</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#06b6d4;">
        <div class="stat-icon" style="background:rgba(6,182,212,0.1);"><i class="fas fa-clock" style="color:#06b6d4;"></i></div>
        <div class="stat-info">
          <span class="stat-label">Today</span>
          <span class="stat-value"><?= $today ?></span>
          <span class="stat-change">Sent today</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#f59e0b;">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);"><i class="fas fa-layer-group" style="color:#f59e0b;"></i></div>
        <div class="stat-info">
          <span class="stat-label">Types</span>
          <span class="stat-value"><?= $type_count ?></span>
          <span class="stat-change">Active categories</span>
        </div>
      </div>
    </div>

    <!-- Main Table Card -->
    <div class="content-card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="nSearch" class="form-control" placeholder="Search notifications..."></div>
        <div class="filter-tabs">
          <button class="filter-tab active" data-filter="all">All</button>
          <button class="filter-tab" data-filter="booking">Booking</button>
          <button class="filter-tab" data-filter="payment">Payment</button>
          <button class="filter-tab" data-filter="promotion">Promo</button>
          <button class="filter-tab" data-filter="alert">Alert</button>
          <button class="filter-tab" data-filter="system">System</button>
        </div>
        <div class="bulk-actions">
          <form method="POST" style="display:inline;" onsubmit="return confirm('Mark all notifications as read?')">
            <input type="hidden" name="action" value="mark_all_read">
            <button type="submit" class="btn btn-sm btn-success" title="Mark All Read"><i class="fas fa-check-double"></i> Mark All Read</button>
          </form>
          <form method="POST" style="display:inline;" onsubmit="return confirm('Delete all read notifications?')">
            <input type="hidden" name="action" value="delete_read">
            <button type="submit" class="btn btn-sm btn-danger" title="Delete Read"><i class="fas fa-trash"></i> Clear Read</button>
          </form>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="data-table" id="nTable">
          <thead><tr>
            <th></th>
            <th>#</th>
            <th>User</th>
            <th>Title & Message</th>
            <th>Type</th>
            <th>Booking</th>
            <th>Date</th>
            <th>Actions</th>
          </tr></thead>
          <tbody>
          <?php $i=1; while($n = mysqli_fetch_assoc($notifications)): 
            $type_icons = [
              'booking'=>'fa-ticket-alt', 'payment'=>'fa-credit-card', 
              'promotion'=>'fa-bullhorn', 'alert'=>'fa-exclamation-triangle', 'system'=>'fa-cog'
            ];
            $icon = $type_icons[$n['type']] ?? 'fa-bell';
          ?>
          <tr data-type="<?= htmlspecialchars($n['type']) ?>">
            <td><span class="read-dot <?= $n['is_read'] ? 'read' : 'unread' ?>"></span></td>
            <td><?= $i++ ?></td>
            <td>
              <div class="user-cell">
                <div class="user-cell-avatar"><?= strtoupper(substr($n['user_name'] ?? 'U', 0, 1)) ?></div>
                <div>
                  <div class="user-cell-name"><?= htmlspecialchars($n['user_name'] ?? 'Unknown User') ?></div>
                  <div class="user-cell-email"><?= htmlspecialchars($n['user_email'] ?? 'N/A') ?></div>
                </div>
              </div>
            </td>
            <td>
              <div class="notif-title"><?= htmlspecialchars($n['title'] ?? '') ?></div>
              <div class="msg-preview"><?= htmlspecialchars($n['message'] ?? '') ?></div>
            </td>
            <td><span class="type-badge <?= $n['type'] ?>"><i class="fas <?= $icon ?>"></i> <?= ucfirst($n['type']) ?></span></td>
            <td><?= $n['booking_id'] ? '<span class="booking-id"><i class="fas fa-hashtag"></i>'.$n['booking_id'].'</span>' : '<span style="color:var(--text-muted);">—</span>' ?></td>
            <td style="white-space:nowrap;"><?= date('d M, h:i A', strtotime($n['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:4px;">
                <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View" onclick="openViewNotifModal('<?= htmlspecialchars(addslashes($n['user_name'] ?? 'Unknown')) ?>', '<?= htmlspecialchars(addslashes($n['user_email'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($n['title'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($n['message'] ?? '')) ?>', '<?= $n['type'] ?>', '<?= $n['is_read'] ? 'Read' : 'Unread' ?>', '<?= $n['booking_id'] ?? '' ?>', '<?= date('F j, Y, g:i a', strtotime($n['created_at'])) ?>')"><i class="fas fa-eye"></i></button>

                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="toggle_read">
                  <input type="hidden" name="id" value="<?= $n['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-primary" title="Toggle Read"><i class="fas fa-<?= $n['is_read'] ? 'envelope-open' : 'envelope' ?>"></i></button>
                </form>

                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this notification?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $n['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <?php if($total == 0): ?>
      <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <p>No notifications yet. Send one to get started!</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Send Notification Modal -->
<div class="modal-overlay" id="sendModal">
  <div class="modal" style="max-width:580px;">
    <div class="modal-header">
      <span class="modal-title"><i class="fas fa-paper-plane" style="color:#a855f7;margin-right:8px;"></i>Send Notification</span>
      <button class="modal-close" onclick="closeModal('sendModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="sendForm" novalidate>
      <input type="hidden" name="action" value="create" id="sendAction">
      <div class="modal-body">

        <!-- Recipient Toggle -->
        <div class="recipient-toggle">
          <button type="button" class="recipient-toggle-btn active" onclick="setRecipient('single', this)">Single User</button>
          <button type="button" class="recipient-toggle-btn" onclick="setRecipient('all', this)">All Users</button>
        </div>

        <div class="form-group" id="userSelectGroup">
          <label class="form-label">Recipient</label>
          <select name="user_id" id="sendUserId" class="form-control">
            <option value="">— Select a user —</option>
            <?php foreach ($user_list as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['fullname']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <span class="error-msg" id="err_user" style="display:none;">Please select a user</span>
        </div>

        <div class="form-grid" style="margin-top:12px;">
          <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" id="sendType" class="form-control">
              <option value="system">System</option>
              <option value="booking">Booking</option>
              <option value="payment">Payment</option>
              <option value="promotion">Promotion</option>
              <option value="alert">Alert</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Booking ID <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
            <input type="number" name="booking_id" class="form-control" placeholder="e.g. 5">
          </div>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
            <label class="form-label">Title</label>
            <span id="titleCounter" style="font-size:10px; color:var(--text-muted); font-variant-numeric:tabular-nums;">0 / 200</span>
          </div>
          <input type="text" name="title" id="sendTitle" class="form-control" placeholder="Notification headline..." maxlength="200">
          <span class="error-msg" id="err_title" style="display:none;"><i class="fas fa-exclamation-circle" style="margin-right:5px;"></i> Title is required</span>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
            <label class="form-label">Message</label>
            <span id="msgCounter" style="font-size:10px; color:var(--text-muted); font-variant-numeric:tabular-nums;">0 / 1000</span>
          </div>
          <textarea name="message" id="sendMsg" class="form-control" rows="4" placeholder="Write the notification message here..." maxlength="1000"></textarea>
          <span class="error-msg" id="err_msg" style="display:none;"><i class="fas fa-exclamation-circle" style="margin-right:5px;"></i> Message content cannot be empty</span>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('sendModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="submitBtn" style="min-width:160px; justify-content:center;"><i class="fas fa-paper-plane" style="margin-right:8px;"></i> Send Notification</button>
      </div>
    </form>
  </div>
</div>

<!-- View Notification Modal -->
<div class="modal-overlay" id="viewNotifModal">
  <div class="modal" style="max-width:550px;">
    <div class="modal-header">
      <span class="modal-title">Notification Details</span>
      <button class="modal-close" onclick="closeModal('viewNotifModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div style="display:flex; align-items:center; gap:15px; margin-bottom:20px;">
        <div id="vn_avatar" style="width:50px; height:50px; border-radius:12px; background:linear-gradient(135deg,#a855f7,#06b6d4); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:20px;"></div>
        <div>
          <h4 id="vn_user" style="color:#fff; font-size:18px; font-weight:700; margin:0;"></h4>
          <p id="vn_email" style="color:var(--accent-cyan); font-size:13px; margin:0;"></p>
        </div>
        <div style="margin-left:auto;" id="vn_type_badge"></div>
      </div>

      <div class="content-card" style="background:rgba(255,255,255,0.03); border:1px solid var(--border); padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
          <label style="font-size:10px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px;">Notification Content</label>
          <span id="vn_status_badge" style="padding:3px 10px; border-radius:50px; font-size:10px; font-weight:700;"></span>
        </div>
        <h3 id="vn_title" style="font-size:17px; font-weight:700; color:#fff; margin-bottom:10px;"></h3>
        <div id="vn_message" style="color:var(--text-secondary); line-height:1.7; font-size:15px; white-space:pre-wrap;"></div>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
        <div id="vn_booking" style="font-size:12px; color:var(--text-muted);"></div>
        <div id="vn_date" style="font-size:11px; color:var(--text-muted);"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewNotifModal')">Close</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
// Search
filterTable('nSearch', 'nTable');

// Type filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('#nTable tbody tr').forEach(row => {
      if (filter === 'all' || row.dataset.type === filter) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});

// Recipient toggle (single vs all users)
let recipientMode = 'single';
function setRecipient(mode, btn) {
  recipientMode = mode;
  document.querySelectorAll('.recipient-toggle-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const group = document.getElementById('userSelectGroup');
  const actionInput = document.getElementById('sendAction');
  
  // Clear user-specific errors when switching
  const userSelect = document.getElementById('sendUserId');
  userSelect.style.borderColor = '';
  document.getElementById('err_user').style.display = 'none';

  if (mode === 'all') {
    group.style.display = 'none';
    actionInput.value = 'send_all';
  } else {
    group.style.display = '';
    actionInput.value = 'create';
  }
}

// REAL-TIME VALIDATION UTILITY
function validateField(id, errId, condition) {
  const el = document.getElementById(id);
  const err = document.getElementById(errId);
  if (condition) {
    el.style.borderColor = '';
    err.style.display = 'none';
    return true;
  } else {
    el.style.borderColor = '#ef4444';
    err.style.display = 'block';
    return false;
  }
}

// Title Input Handling
const titleInp = document.getElementById('sendTitle');
const titleCnt = document.getElementById('titleCounter');
titleInp.addEventListener('input', function() {
  const len = this.value.length;
  titleCnt.innerText = `${len} / 200`;
  titleCnt.style.color = len >= 190 ? '#ef4444' : (len > 0 ? '#10b981' : 'var(--text-muted)');
  validateField('sendTitle', 'err_title', this.value.trim().length > 0);
});

// Message Input Handling
const msgInp = document.getElementById('sendMsg');
const msgCnt = document.getElementById('msgCounter');
msgInp.addEventListener('input', function() {
  const len = this.value.length;
  msgCnt.innerText = `${len} / 1000`;
  msgCnt.style.color = len >= 950 ? '#ef4444' : (len > 0 ? '#10b981' : 'var(--text-muted)');
  validateField('sendMsg', 'err_msg', this.value.trim().length > 0);
});

// User Select Handling
document.getElementById('sendUserId').addEventListener('change', function() {
  validateField('sendUserId', 'err_user', !!this.value);
});

// Form submission logic
document.getElementById('sendForm').addEventListener('submit', function(e) {
  let isAllValid = true;
  const modal = this.closest('.modal');
  const btn = document.getElementById('submitBtn');

  // Validate all fields
  const titleValid = validateField('sendTitle', 'err_title', titleInp.value.trim().length > 0);
  const msgValid   = validateField('sendMsg', 'err_msg', msgInp.value.trim().length > 0);
  let userValid    = true;
  
  if (recipientMode === 'single') {
    userValid = validateField('sendUserId', 'err_user', !!document.getElementById('sendUserId').value);
  }

  isAllValid = titleValid && msgValid && userValid;

  if (!isAllValid) {
    e.preventDefault();
    showToast('Please correct the missing information', 'error');
  } else {
    // Perfect submission UX
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
    btn.style.opacity = '0.7';
  }
});

// Clear errors on input
['sendTitle','sendMsg','sendUserId'].forEach(id => {
  const el = document.getElementById(id);
  if (el) el.addEventListener('input', function() { this.style.borderColor = ''; });
});

// View notification modal
function openViewNotifModal(user, email, title, message, type, status, bookingId, date) {
  document.getElementById('vn_avatar').innerText = user.charAt(0).toUpperCase();
  document.getElementById('vn_user').innerText = user;
  document.getElementById('vn_email').innerText = email;
  document.getElementById('vn_title').innerText = title;
  document.getElementById('vn_message').innerText = message;
  document.getElementById('vn_date').innerText = 'Sent: ' + date;

  // Type badge
  const typeIcons = {
    booking: 'fa-ticket-alt', payment: 'fa-credit-card',
    promotion: 'fa-bullhorn', alert: 'fa-exclamation-triangle', system: 'fa-cog'
  };
  document.getElementById('vn_type_badge').innerHTML = 
    `<span class="type-badge ${type}"><i class="fas ${typeIcons[type] || 'fa-bell'}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}</span>`;

  // Status badge
  const statusBadge = document.getElementById('vn_status_badge');
  if (status === 'Unread') {
    statusBadge.innerHTML = 'UNREAD';
    statusBadge.style.background = 'rgba(168,85,247,0.15)';
    statusBadge.style.color = '#a855f7';
    statusBadge.style.border = '1px solid rgba(168,85,247,0.3)';
  } else {
    statusBadge.innerHTML = 'READ';
    statusBadge.style.background = 'rgba(255,255,255,0.05)';
    statusBadge.style.color = 'var(--text-muted)';
    statusBadge.style.border = '1px solid var(--border)';
  }

  // Booking ID
  if (bookingId) {
    document.getElementById('vn_booking').innerHTML = '<i class="fas fa-link" style="margin-right:4px;"></i> Linked to Booking #' + bookingId;
  } else {
    document.getElementById('vn_booking').innerHTML = '';
  }

  openModal('viewNotifModal');
}
</script>
</body>
</html>
