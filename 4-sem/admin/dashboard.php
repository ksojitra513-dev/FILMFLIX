<?php
require_once 'includes/auth.php';
require_once '../config.php';

// Fetch stats
$total_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM user"))['cnt'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM bookings"))['cnt'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='Paid'"))['total'] ?? 0;
$total_movies = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM movies"))['cnt'];
$total_theaters = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM theaters"))['cnt'];
$total_feedback = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM feedback"))['cnt'];
$total_contacts = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM contact"))['cnt'];
$total_offers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM movie_offers"))['cnt'];
$total_notifications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM notifications"))['cnt'];
$unread_notifications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM notifications WHERE is_read=0"))['cnt'];

// Advanced Stats
$top_movie = mysqli_fetch_assoc(mysqli_query($con, "SELECT movie_title, COUNT(*) as bookings FROM bookings GROUP BY movie_title ORDER BY bookings DESC LIMIT 1"))['movie_title'] ?? 'N/A';
$last_month_rev = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='Paid' AND booking_date < DATE_SUB(NOW(), INTERVAL 1 MONTH)"))['total'] ?? 0;
$rev_growth = $last_month_rev > 0 ? (($total_revenue - $last_month_rev) / $last_month_rev) * 100 : 100;

// Recent bookings
$recent_bookings = mysqli_query($con, "SELECT * FROM bookings ORDER BY booking_date DESC LIMIT 5");

// Recent contacts
$recent_contacts = mysqli_query($con, "SELECT * FROM contact ORDER BY created_at DESC LIMIT 4");

$avg_rating = mysqli_fetch_assoc(mysqli_query($con, "SELECT AVG(star_rating) as avg_r FROM feedback"))['avg_r'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
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
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="page-subtitle">Welcome back! Here's what's happening with FilmFlix today.</p>
      </div>
      <div class="page-actions">
        <span class="badge-live"><span class="pulse"></span> Live</span>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card" style="--accent:#a855f7">
        <div class="stat-icon" style="background:rgba(168,85,247,.15)"><i class="fas fa-users"></i></div>
        <div class="stat-info">
          <span class="stat-label">Total Users</span>
          <span class="stat-value"><?= number_format($total_users) ?></span>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> Active</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#06b6d4">
        <div class="stat-icon" style="background:rgba(6,182,212,.15)"><i class="fas fa-ticket-alt"></i></div>
        <div class="stat-info">
          <span class="stat-label">Total Bookings</span>
          <span class="stat-value"><?= number_format($total_bookings) ?></span>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> All time</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#10b981">
        <div class="stat-icon" style="background:rgba(16,185,129,.15)"><i class="fas fa-indian-rupee-sign"></i></div>
        <div class="stat-info">
          <span class="stat-label">Total Revenue</span>
          <span class="stat-value">₹<?= number_format($total_revenue, 0) ?></span>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> Paid</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#f59e0b">
        <div class="stat-icon" style="background:rgba(245,158,11,.15)"><i class="fas fa-film"></i></div>
        <div class="stat-info">
          <span class="stat-label">Movies</span>
          <span class="stat-value"><?= number_format($total_movies) ?></span>
          <span class="stat-change"><i class="fas fa-circle"></i> In library</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#ef4444">
        <div class="stat-icon" style="background:rgba(239,68,68,.15)"><i class="fas fa-building"></i></div>
        <div class="stat-info">
          <span class="stat-label">Theaters</span>
          <span class="stat-value"><?= number_format($total_theaters) ?></span>
          <span class="stat-change"><i class="fas fa-circle"></i> Partner</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#8b5cf6">
        <div class="stat-icon" style="background:rgba(139,92,246,.15)"><i class="fas fa-star"></i></div>
        <div class="stat-info">
          <span class="stat-label">Avg Rating</span>
          <span class="stat-value"><?= number_format($avg_rating, 1) ?>/5</span>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> <?= $total_feedback ?> reviews</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#ec4899">
        <div class="stat-icon" style="background:rgba(236,72,153,.15)"><i class="fas fa-envelope"></i></div>
        <div class="stat-info">
          <span class="stat-label">Contact Messages</span>
          <span class="stat-value"><?= number_format($total_contacts) ?></span>
          <span class="stat-change"><i class="fas fa-circle"></i> Total</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#14b8a6">
        <div class="stat-icon" style="background:rgba(20,184,166,.15)"><i class="fas fa-tags"></i></div>
        <div class="stat-info">
          <span class="stat-label">Active Offers</span>
          <span class="stat-value"><?= number_format($total_offers) ?></span>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> Running</span>
        </div>
      </div>
      <div class="stat-card" style="--accent:#a855f7">
        <div class="stat-icon" style="background:rgba(168,85,247,.15)"><i class="fas fa-bell"></i></div>
        <div class="stat-info">
          <span class="stat-label">Notifications</span>
          <span class="stat-value"><?= number_format($total_notifications) ?></span>
          <span class="stat-change <?= $unread_notifications > 0 ? 'up' : '' ?>"><i class="fas fa-envelope"></i> <?= $unread_notifications ?> unread</span>
        </div>
      </div>
    </div>

    <!-- Tables Row -->
    <div class="tables-row">
      <!-- Recent Bookings -->
      <div class="table-card wide">
        <div class="table-card-header">
          <h3><i class="fas fa-ticket-alt"></i> Recent Bookings</h3>
          <a href="bookings.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>Booking #</th>
                <th>Movie</th>
                <th>Show Time</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while($b = mysqli_fetch_assoc($recent_bookings)): ?>
              <tr>
                <td><span class="booking-id"><i class="fas fa-hashtag"></i><?= htmlspecialchars($b['booking_number']) ?></span></td>
                <td><?= htmlspecialchars($b['movie_title']) ?></td>
                <td><?= date('h:i A', strtotime($b['show_time'])) ?></td>
                <td><span class="badge-seats"><?= htmlspecialchars($b['seats_booked']) ?></span></td>
                <td><strong>₹<?= number_format($b['total_amount'], 2) ?></strong></td>
                <td><span class="status-badge <?= strtolower($b['payment_status']) ?>"><?= $b['payment_status'] ?></span></td>
                <td>
                   <button type="button" class="btn btn-sm" style="background:rgba(6,182,212,0.1); color:#06b6d4; border:none;" onclick="openViewBookingModal('<?= htmlspecialchars(addslashes($b['booking_number'])) ?>', '<?= htmlspecialchars(addslashes($b['movie_title'])) ?>', '<?= htmlspecialchars(addslashes($b['seats_booked'])) ?>', '<?= htmlspecialchars(addslashes($b['total_amount'])) ?>', '<?= htmlspecialchars(addslashes($b['payment_status'])) ?>', '<?= htmlspecialchars(addslashes($b['booking_status'])) ?>', '<?= htmlspecialchars(addslashes($b['payment_method'])) ?>', '<?= date('d M Y, h:i A', strtotime($b['booking_date'])) ?>')"><i class="fas fa-eye"></i></button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Recent Contacts -->
      <div class="table-card narrow">
        <div class="table-card-header">
          <h3><i class="fas fa-envelope"></i> Recent Contact</h3>
          <a href="contacts.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="contact-list">
          <?php while($c = mysqli_fetch_assoc($recent_contacts)): ?>
          <div class="contact-item">
            <div class="contact-avatar"><?= strtoupper(substr($c['fullname'], 0, 1)) ?></div>
            <div class="contact-info">
              <span class="contact-name"><?= htmlspecialchars($c['fullname']) ?></span>
              <span class="contact-email"><?= htmlspecialchars($c['email']) ?></span>
              <span class="contact-msg"><?= htmlspecialchars(substr($c['message'], 0, 50)) ?>...</span>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>

  </div><!-- end page-content -->
</div><!-- end main-content -->

<div class="modal-overlay" id="viewBookingModal">
  <div class="modal" style="max-width:550px;">
    <div class="modal-header">
      <span class="modal-title">Booking Ticket Overview</span>
      <button class="modal-close" onclick="closeModal('viewBookingModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
       <div style="background:rgba(255,255,255,0.03); border:1px dashed var(--border); border-radius:16px; padding:25px; text-align:center;">
          <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:2px; margin-bottom:5px;">Booking ID</div>
          <div id="vb_id" style="font-size:24px; font-weight:800; color:var(--accent); margin-bottom:20px;"></div>
          
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; text-align:left; border-top:1px solid rgba(255,255,255,0.05); padding-top:20px;">
             <div><label style="color:var(--text-muted); font-size:10px;">MOVIE</label><div id="vb_movie" style="color:#fff; font-weight:600;"></div></div>
             <div><label style="color:var(--text-muted); font-size:10px;">SEATS</label><div id="vb_seats" style="color:#fff; font-weight:600;"></div></div>
             <div><label style="color:var(--text-muted); font-size:10px;">AMOUNT</label><div id="vb_amount" style="color:var(--accent); font-weight:700;"></div></div>
             <div><label style="color:var(--text-muted); font-size:10px;">DATE</label><div id="vb_date" style="color:#fff; font-size:13px;"></div></div>
          </div>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewBookingModal')">Done</button>
    </div>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openViewBookingModal(id, movie, seats, amount, pay_status, book_status, method, date) {
    document.getElementById('vb_id').innerText = "#" + id;
    document.getElementById('vb_movie').innerText = movie;
    document.getElementById('vb_seats').innerText = seats;
    document.getElementById('vb_amount').innerText = "₹" + parseFloat(amount).toLocaleString();
    document.getElementById('vb_date').innerText = date;
    openModal('viewBookingModal');
}
</script>
</body>
</html>

