<?php
session_start();
include 'config.php'; 

// Jab Confirm Logout par click ho
if (isset($_GET['action']) && $_GET['action'] == 'confirm') {
    
    // --- DELETE LOGIC: Email ko database se mita do ---
    if (isset($_SESSION['user_email'])) {
        $email_to_delete = $_SESSION['user_email'];
        mysqli_query($con, "DELETE FROM user WHERE email = '$email_to_delete'");
    }

    // Session clear karein
    $_SESSION = array();
    session_destroy();

    // Cache clear headers
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Wapas registration page par bhej do
    header("Location: c2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #0c0c0c; color: white; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .stat-card { background: #181818; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; }
        .btn-logout { background-color: #e50914; color: white; padding: 12px; border-radius: 5px; text-decoration: none; display: block; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="stat-card">
        <i class="fas fa-sign-out-alt text-danger mb-3" style="font-size: 3rem;"></i>
        <h2>Logout</h2>
        <p class="text-secondary">Kya aap logout karke apna account delete karna chahte hain? (Testing Mode)</p>
        <a href="logout.php?action=confirm" class="btn-logout">Confirm & Delete Account</a>
        <br>
        <a href="c.php" style="color: #888; text-decoration: none;">Cancel</a>
    </div>
</body>
</html>