<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if admin_id is not set in session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Re-verify admin status against database if needed for extra security
// require_once '../config.php';
// $admin_id = $_SESSION['admin_id'];
// $check = mysqli_query($con, "SELECT id FROM user WHERE id = $admin_id AND role = 'admin' AND status = 'active'");
// if (mysqli_num_rows($check) === 0) { session_destroy(); header("Location: index.php"); exit(); }
?>
