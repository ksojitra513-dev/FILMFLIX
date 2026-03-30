<?php
session_start();

// Unset all admin-related session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_avatar']);

// Destroy the session completely if desired
// session_destroy();

// Redirect back to login page
header("Location: login.php?msg=logged_out");
exit();
?>
