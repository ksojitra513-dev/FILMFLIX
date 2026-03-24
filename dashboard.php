<?php 
session_start();
include_once 'config.php';

if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$u_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE id = '$u_id' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

$user_name = $user['fullname']; 
$db_image  = $user['image']; 

if(!empty($db_image) && file_exists("uploads/" . $db_image)) {
    $profile_img = "uploads/" . $db_image;
} else {
    $profile_img = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=e50914&color=fff&size=128";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --netflix-red: #e50914; --dark-bg: #0c0c0c; --card-bg: #111; }
        body { background-color: var(--dark-bg); color: white; font-family: 'Segoe UI', sans-serif; margin: 0; overflow-x: hidden; }
        .navbar { position: fixed; top: 0; left: 0; width: 100%; height: 70px; z-index: 1100; background-color: #000; border-bottom: 1px solid #222; padding: 0 25px; }
        .navbar-brand { color: var(--netflix-red) !important; font-weight: 800; font-size: 1.6rem; text-decoration: none; }
        .sidebar { position: fixed; top: 70px; left: 0; width: 260px; height: 100vh; background-color: #000; padding-top: 20px; z-index: 1000; border-right: 1px solid #111; }
        .sidebar-user-section { text-align: center; padding: 20px 0; border-bottom: 1px solid #111; margin-bottom: 10px; }
        .sidebar-img { width: 90px; height: 90px; border-radius: 50%; border: 3px solid var(--netflix-red); object-fit: cover; margin-bottom: 10px; }
        .sidebar-name { font-size: 1.1rem; font-weight: 600; color: #fff; margin: 0; }
        .nav-link { color: #808080; padding: 15px 30px; display: flex; align-items: center; text-decoration: none; transition: 0.3s; }
        .nav-link i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: var(--netflix-red); background: rgba(229, 9, 20, 0.05); border-right: 4px solid var(--netflix-red); }
        .main-content { margin-left: 260px; padding: 110px 40px; }
        .stat-card { background: var(--card-bg); border: 1px solid #222; border-radius: 12px; padding: 25px; transition: 0.3s; display: flex; align-items: center; justify-content: space-between; }
        .stat-card:hover { border-color: var(--netflix-red); transform: translateY(-5px); }
        @media (max-width: 992px) { .sidebar { width: 80px; } .sidebar-user-section, .sidebar span { display: none; } .main-content { margin-left: 80px; } }
    </style>
</head>
<body>
    <nav class="navbar d-flex justify-content-between align-items-center">
        
        <div class="d-flex align-items-center gap-4">
            <a class="navbar-brand" href="dashboard.php"><span style="color: white;">FILM</span>FLIX</a>
            <a href="c.php" class="text-white text-decoration-none d-none d-md-block small fw-bold">HOME</a>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3 d-none d-md-block small fw-bold"><?php echo htmlspecialchars($user_name); ?></span>
            <img src="<?php echo $profile_img; ?>" class="rounded-circle shadow" width="40" height="40" style="object-fit: cover; border: 1px solid #444;">
        </div>
    </nav>

    <div class="sidebar">
        <div class="sidebar-user-section">
            <img src="<?php echo $profile_img; ?>" class="sidebar-img">
            <h6 class="sidebar-name"><?php echo htmlspecialchars($user_name); ?></h6>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
            <a class="nav-link" href="watch.php"><i class="fas fa-heart"></i> <span>Watchlist</span></a>
            <a class="nav-link" href="profile.php"><i class="fas fa-user-edit"></i> <span>Edit Profile</span></a>
            <a class="nav-link" href="change_password.php"><i class="fas fa-lock"></i> <span>Change Password</span></a>
            <div style="border-top: 1px solid #222; margin: 15px 0;"></div>
            <a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <h1 class="fw-bold mb-4">Welcome back, <span style="color:var(--netflix-red);"><?php echo htmlspecialchars($user_name); ?></span></h1>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div><p class="text-secondary mb-1 small fw-bold">MEMBERSHIP</p><h4 class="fw-bold m-0">PREMIUM 4K</h4></div>
                    <i class="fas fa-gem text-warning fs-1 opacity-25"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div><p class="text-secondary mb-1 small fw-bold">WATCHED</p><h4 class="fw-bold m-0">124 Movies</h4></div>
                    <i class="fas fa-play-circle text-danger fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>