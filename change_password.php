<?php 
session_start();
include_once 'config.php';

// 1. Authentication Check
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$u_id = $_SESSION['user_id'];

// 2. User ki current details (Sidebar ke liye wahi purana logic)
$query = "SELECT * FROM user WHERE id = '$u_id' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

$user_name = $user['fullname']; 
$user_email = $user['email'];
$db_pass = $user['password']; 
$db_image = $user['image'];

// Profile Image logic for Sidebar
if(!empty($db_image) && file_exists("uploads/" . $db_image)) {
    $profile_img = "uploads/" . $db_image;
} else {
    $profile_img = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=e50914&color=fff&size=128";
}

// 3. Password Update Logic
if (isset($_POST['change_pwd_btn'])) {
    $current_pass = mysqli_real_escape_string($con, $_POST['current_password']);
    $new_pass = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_pass = $_POST['confirm_password'];

    if ($current_pass === $db_pass) {
        if ($new_pass === $confirm_pass) {
            $update_sql = "UPDATE user SET password = '$new_pass' WHERE id = '$u_id'";
            if (mysqli_query($con, $update_sql)) {
                // Success Message Session mein save karein
                $_SESSION['status'] = "Password Updated Successfully!"; 
                header("Location: change_password.php"); 
                exit();
            } else {
                $_SESSION['error'] = "Database Error: " . mysqli_error($con);
            }
        } else {
            $_SESSION['error'] = "New passwords do not match!";
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect!";
    }
    header("Location: change_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --netflix-red: #e50914; --dark-bg: #0c0c0c; --card-bg: #111; --success-green: #2ecc71; }
        body { background-color: var(--dark-bg); color: white; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* Navbar */
        .navbar { position: fixed; top: 0; left: 0; width: 100%; height: 70px; z-index: 1100; background-color: #000; border-bottom: 1px solid #222; padding: 0 25px; }
        .navbar-brand { color: var(--netflix-red) !important; font-weight: 800; font-size: 1.6rem; text-decoration: none; }

        /* Sidebar (No Changes) */
        .sidebar { position: fixed; top: 70px; left: 0; width: 260px; height: 100vh; background-color: #000; padding-top: 20px; z-index: 1000; border-right: 1px solid #111; }
        .sidebar-user-section { text-align: center; padding: 20px 0; border-bottom: 1px solid #111; margin-bottom: 10px; }
        .sidebar-img { width: 90px; height: 90px; border-radius: 50%; border: 3px solid var(--netflix-red); object-fit: cover; margin-bottom: 10px; }
        .sidebar-name { font-size: 1.1rem; font-weight: 600; color: #fff; margin: 0; }

        .nav-link { color: #808080; padding: 15px 30px; display: flex; align-items: center; text-decoration: none; transition: 0.3s; }
        .nav-link i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: var(--netflix-red); background: rgba(229, 9, 20, 0.05); border-right: 4px solid var(--netflix-red); }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 110px 40px; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        .profile-card { background: var(--card-bg); border: 1px solid #222; border-radius: 12px; padding: 40px; width: 100%; max-width: 550px; }

        .form-control { background: #222 !important; border: 1px solid #444 !important; color: white !important; padding: 12px; }
        .error { color: var(--netflix-red); font-size: 0.85rem; margin-top: 5px; font-weight: 500; }

        /* Alert Box Styling */
        .status-msg { padding: 12px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .status-success { background-color: var(--success-green); color: white; }
        .status-error { background-color: var(--netflix-red); color: white; }

        @media (max-width: 992px) { .sidebar { width: 80px; } .sidebar-user-section, .sidebar span { display: none; } .main-content { margin-left: 80px; } }
    </style>
</head>
<body>

    <nav class="navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <a class="navbar-brand" href="dashboard.php">FILMFLIX</a>
            <a href="c.php" class="text-white text-decoration-none d-none d-md-block small fw-bold">HOME</a>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3 d-none d-md-block small fw-bold"><?php echo htmlspecialchars($user_name); ?></span>
            <img src="<?php echo $profile_img; ?>" class="rounded-circle shadow" width="40" height="40" style="object-fit: cover;">
        </div>
    </nav>

    <div class="sidebar">
        <div class="sidebar-user-section">
            <img src="<?php echo $profile_img; ?>" class="sidebar-img">
            <h6 class="sidebar-name"><?php echo htmlspecialchars($user_name); ?></h6>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
            <a class="nav-link" href="watch.php"><i class="fas fa-heart"></i> <span>Watchlist</span></a>
            <a class="nav-link" href="profile.php"><i class="fas fa-user-edit"></i> <span>Edit Profile</span></a>
            <a class="nav-link active" href="change_password.php"><i class="fas fa-lock"></i> <span>Change Password</span></a>
            <div style="border-top:1px solid #222; margin:15px 0;"></div>
            <a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <div style="width: 100%; max-width: 550px;">
            
            <?php if(isset($_SESSION['status'])): ?>
                <div class="status-msg status-success shadow" id="msg-alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['status']; unset($_SESSION['status']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="status-msg status-error shadow" id="msg-alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="profile-card">
                <h4 class="fw-bold mb-4 text-center">
                    <i class="fas fa-shield-alt me-2 text-danger"></i> SECURITY SETTINGS
                </h4>
                
                <form id="changePasswordForm" action="" method="POST">
                    <div class="mb-3">
                        <label class="small text-secondary mb-1 fw-bold">USER EMAIL</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="small text-secondary mb-1 fw-bold">CURRENT PASSWORD</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Enter old password">
                    </div>
                    
                    <div class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="small text-secondary mb-1 fw-bold">NEW PASSWORD</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Min 4 chars">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-secondary mb-1 fw-bold">CONFIRM NEW</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="change_pwd_btn" class="btn w-100 fw-bold shadow" style="background:var(--netflix-red); color:white; padding:12px;">
                        UPDATE & CONTINUE
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            // Auto-hide alert after 5 seconds
            setTimeout(function() {
                $('#msg-alert').fadeOut('slow');
            }, 5000);

            $("#changePasswordForm").validate({
                rules: {
                    current_password: { required: true },
                    new_password: { required: true, minlength: 4 },
                    confirm_password: { required: true, equalTo: "#new_password" }
                },
                messages: {
                    confirm_password: { equalTo: "Passwords do not match!" }
                },
                errorElement: 'span',
                errorClass: "error"
            });
        });
    </script>
</body>
</html>