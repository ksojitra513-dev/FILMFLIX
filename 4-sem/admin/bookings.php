<?php
require_once 'includes/auth.php';
require_once '../config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM bookings WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Booking deleted.'];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $ps = mysqli_real_escape_string($con, $_POST['payment_status']);
        $bs = mysqli_real_escape_string($con, $_POST['booking_status']);
        mysqli_query($con, "UPDATE bookings SET payment_status='$ps', booking_status='$bs' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Booking updated.'];
    }
}

$bookings = mysqli_query($con, "SELECT * FROM bookings ORDER BY booking_date DESC");
$total = mysqli_num_rows($bookings);
$revenue = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(total_amount) as t FROM bookings WHERE payment_status='Paid'"))['t']??0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Bookings</title>
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
        <h1 class="page-title">Booking Management</h1>
        <p class="page-subtitle"><?= $total ?> total bookings • ₹<?= number_format($revenue,0) ?> revenue</p>
      </div>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="bookSearch" class="form-control" placeholder="Search bookings..."></div>
      </div>
      <div class="table-wrapper">
        <table class="data-table" id="bookingsTable">
          <thead><tr>
            <th>#</th><th>Booking ID</th><th>Movie</th><th>Show Time</th><th>Seats</th><th>Amount</th><th>Method</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php $i=1; mysqli_data_seek($bookings,0); while($b = mysqli_fetch_assoc($bookings)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><span class="booking-id"><i class="fas fa-hashtag"></i><?= htmlspecialchars($b['booking_number']) ?></span></td>
            <td><?= htmlspecialchars($b['movie_title']) ?></td>
            <td><?= date('h:i A', strtotime($b['show_time'])) ?></td>
            <td><span class="badge-seats"><?= htmlspecialchars($b['seats_booked']) ?></span></td>
            <td><strong>₹<?= number_format($b['total_amount'],2) ?></strong></td>
            <td><?= htmlspecialchars($b['payment_method']) ?></td>
            <td><span class="status-badge <?= strtolower($b['payment_status']) ?>"><?= $b['payment_status'] ?></span></td>
            <td><span class="status-badge <?= strtolower($b['booking_status']) ?>"><?= $b['booking_status'] ?></span></td>
            <td><?= date('d M Y, h:i A', strtotime($b['booking_date'])) ?></td>
            <td>
              <div style="display:flex;gap:4px;">
                <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:none;" title="View Details" onclick="openViewBookingModal('<?= htmlspecialchars(addslashes($b['booking_number'])) ?>', '<?= htmlspecialchars(addslashes($b['movie_title'])) ?>', '<?= htmlspecialchars(addslashes($b['seats_booked'])) ?>', '<?= htmlspecialchars(addslashes($b['total_amount'])) ?>', '<?= htmlspecialchars(addslashes($b['payment_status'])) ?>', '<?= htmlspecialchars(addslashes($b['booking_status'])) ?>', '<?= htmlspecialchars(addslashes($b['payment_method'])) ?>', '<?= date('d M Y, h:i A', strtotime($b['booking_date'])) ?>')"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-primary" onclick="openEditModal(<?= $b['id'] ?>,'<?= $b['payment_status'] ?>','<?= $b['booking_status'] ?>')" title="Edit"><i class="fas fa-edit"></i></button>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Cancel this booking?')">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="id" value="<?= $b['id'] ?>">
                  <input type="hidden" name="payment_status" value="<?= $b['payment_status'] ?>">
                  <input type="hidden" name="booking_status" value="Cancelled">
                  <button type="submit" class="btn btn-sm" title="Cancel Booking" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:none;"><i class="fas fa-ban"></i></button>
                </form>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete booking record permanently?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $b['id'] ?>">
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

<!-- Edit Status Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title"><i class="fas fa-edit" style="color:#a855f7;margin-right:8px;"></i>Update Booking Status</span>
      <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="update_status">
      <input type="hidden" name="id" id="editId">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" id="editPayment" class="form-control">
              <option value="Paid">Paid</option>
              <option value="Pending">Pending</option>
              <option value="Failed">Failed</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Booking Status</label>
            <select name="booking_status" id="editBooking" class="form-control">
              <option value="Confirmed">Confirmed</option>
              <option value="Cancelled">Cancelled</option>
              <option value="Pending">Pending</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
      </div>
    </form>
  </div>
</div>

  </div>
</div>

<!-- View Booking Modal -->
<div class="modal-overlay" id="viewBookingModal">
  <div class="modal" style="max-width:600px;">
    <div class="modal-header">
      <span class="modal-title">Booking Ticket Details</span>
      <button class="modal-close" onclick="closeModal('viewBookingModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
       <div style="background:rgba(255,255,255,0.03); border:1px dashed var(--border); border-radius:16px; padding:30px; position:relative; overflow:hidden;">
          <!-- Decorators -->
          <div style="position:absolute; top:-20px; left:-20px; width:40px; height:40px; background:var(--bg-main); border-radius:50%;"></div>
          <div style="position:absolute; top:-20px; right:-20px; width:40px; height:40px; background:var(--bg-main); border-radius:50%;"></div>
          <div style="position:absolute; bottom:-20px; left:-20px; width:40px; height:40px; background:var(--bg-main); border-radius:50%;"></div>
          <div style="position:absolute; bottom:-20px; right:-20px; width:40px; height:40px; background:var(--bg-main); border-radius:50%;"></div>
          
          <div style="text-align:center; margin-bottom:25px;">
             <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:2px;">Booking ID</div>
             <div id="vb_id" style="font-size:28px; font-weight:800; color:var(--accent);"></div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; border-top:1px solid rgba(255,255,255,0.05); padding-top:20px;">
             <div>
                <label style="display:block; font-size:11px; color:var(--text-muted); text-transform:uppercase;">Movie Title</label>
                <div id="vb_movie" style="font-size:16px; font-weight:600; color:#fff;"></div>
             </div>
             <div>
                <label style="display:block; font-size:11px; color:var(--text-muted); text-transform:uppercase;">Seats</label>
                <div id="vb_seats" style="font-size:16px; font-weight:600; color:#fff;"></div>
             </div>
             <div>
                <label style="display:block; font-size:11px; color:var(--text-muted); text-transform:uppercase;">Total Amount</label>
                <div id="vb_amount" style="font-size:16px; font-weight:600; color:var(--accent);"></div>
             </div>
             <div>
                <label style="display:block; font-size:11px; color:var(--text-muted); text-transform:uppercase;">Payment Method</label>
                <div id="vb_method" style="font-size:16px; font-weight:600; color:#fff;"></div>
             </div>
             <div>
                <label style="display:block; font-size:11px; color:var(--text-muted); text-transform:uppercase;">Status</label>
                <div id="vb_status" style="margin-top:4px;"></div>
             </div>
             <div>
                <label style="display:block; font-size:11px; color:var(--text-muted); text-transform:uppercase;">Date & Time</label>
                <div id="vb_date" style="font-size:14px; font-weight:500; color:var(--text-secondary);"></div>
             </div>
          </div>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewBookingModal')">Close Ticket</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
filterTable('bookSearch','bookingsTable');

function openViewBookingModal(id, movie, seats, amount, pay_status, book_status, method, date) {
    document.getElementById('vb_id').innerText = "#" + id;
    document.getElementById('vb_movie').innerText = movie;
    document.getElementById('vb_seats').innerText = seats;
    document.getElementById('vb_amount').innerText = "₹" + parseFloat(amount).toLocaleString();
    document.getElementById('vb_method').innerText = method;
    document.getElementById('vb_date').innerText = date;
    
    const statusHtml = `
        <span class="status-badge ${pay_status.toLowerCase()}">${pay_status}</span>
        <span class="status-badge ${book_status.toLowerCase()}">${book_status}</span>
    `;
    document.getElementById('vb_status').innerHTML = statusHtml;
    
    openModal('viewBookingModal');
}

function openEditModal(id, payment, booking) {
  document.getElementById('editId').value = id;
  document.getElementById('editPayment').value = payment;
  document.getElementById('editBooking').value = booking;
  openModal('editModal');
}
</script>
</body>
</html>


