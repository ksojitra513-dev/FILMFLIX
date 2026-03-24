<?php 
session_start();
include_once 'config.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$u_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE id = '$u_id' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

$user_name = $user['fullname']; 
$user_email = $user['email']; // Email fetch kiya
$db_image = $user['image'];

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
    <title>Edit Profile - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --netflix-red: #e50914; --dark-bg: #0c0c0c; --card-bg: #111; }
        body { background-color: var(--dark-bg); color: white; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .navbar { position: fixed; top: 0; left: 0; width: 100%; height: 70px; z-index: 1100; background-color: #000; border-bottom: 1px solid #222; padding: 0 25px; }
        .navbar-brand { color: var(--netflix-red) !important; font-weight: 800; font-size: 1.6rem; text-decoration: none; }
        .sidebar { position: fixed; top: 70px; left: 0; width: 260px; height: 100vh; background-color: #000; padding-top: 20px; z-index: 1000; border-right: 1px solid #111; }
        .sidebar-user-section { text-align: center; padding: 20px 0; border-bottom: 1px solid #111; margin-bottom: 10px; }
        .sidebar-img { width: 90px; height: 90px; border-radius: 50%; border: 3px solid var(--netflix-red); object-fit: cover; margin-bottom: 10px; }
        .sidebar-name { font-size: 1.1rem; font-weight: 600; color: #fff; margin: 0; }
        .nav-link { color: #808080; padding: 15px 30px; display: flex; align-items: center; text-decoration: none; transition: 0.3s; }
        .nav-link i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-link.active { color: var(--netflix-red); background: rgba(229, 9, 20, 0.05); border-right: 4px solid var(--netflix-red); }
        .main-content { margin-left: 260px; padding: 110px 40px; }
        .profile-card { background: var(--card-bg); border: 1px solid #222; border-radius: 12px; padding: 40px; }
        .form-control { background: #222 !important; border: 1px solid #444 !important; color: white !important; padding: 12px; }
        .form-control:focus { border-color: var(--netflix-red) !important; box-shadow: none; }
        @media (max-width: 992px) { .sidebar { width: 80px; } .sidebar-user-section, .sidebar span { display: none; } .main-content { margin-left: 80px; } }
    </style>
</head>
<body>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Profile Updated!', text: 'Details successfully save ho gayi hain.', background: '#111', color: '#fff', confirmButtonColor: '#e50914' });
        window.history.replaceState({}, document.title, "profile.php");
    </script>
    <?php endif; ?>

    <nav class="navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <a class="navbar-brand" href="dashboard.php">FILMFLIX</a>
            <a href="c.php" class="text-white text-decoration-none d-none d-md-block small fw-bold">HOME</a>
        </div>
        <div class="d-flex align-items-center"><span class="me-3 d-none d-md-block small fw-bold"><?php echo htmlspecialchars($user_name); ?></span><img src="<?php echo $profile_img; ?>" class="rounded-circle shadow" width="40" height="40"></div>
    </nav>

    <div class="sidebar">
        <div class="sidebar-user-section"><img src="<?php echo $profile_img; ?>" class="sidebar-img"><h6 class="sidebar-name"><?php echo htmlspecialchars($user_name); ?></h6></div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
            <a class="nav-link" href="watch.php"><i class="fas fa-heart"></i> <span>Watchlist</span></a>
            <a class="nav-link active" href="profile.php"><i class="fas fa-user-edit"></i> <span>Edit Profile</span></a>
            <a class="nav-link" href="change_password.php"><i class="fas fa-lock"></i> <span>Change Password</span></a>
            <div style="border-top:1px solid #222; margin:15px 0;"></div>
            <a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <div class="profile-card">
            <form action="manage_profile.php" method="POST" enctype="multipart/form-data">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center border-end border-secondary">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="<?php echo $profile_img; ?>" id="imgPrev" style="width:140px; height:140px; border-radius:50%; border:3px solid var(--netflix-red); object-fit:cover;">
                            <label for="fileInput" style="position:absolute; bottom:5px; right:5px; background:var(--netflix-red); width:35px; height:35px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer;"><i class="fas fa-camera"></i></label>
                            <input type="file" name="profile_image" id="fileInput" hidden accept="image/*">
                        </div>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($user_name); ?></h5>
                    </div>
                    <div class="col-md-9 ps-md-5">
                        <h4 class="mb-4 fw-bold">EDIT PROFILE</h4>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="small text-secondary mb-1">FULL NAME</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" required></div>
                            <div class="col-md-6"><label class="small text-secondary mb-1">PHONE NUMBER</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['number']); ?>" required></div>
                            
                            <div class="col-md-12"><label class="small text-secondary mb-1">EMAIL ADDRESS</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" required></div>
                            
                            <div class="col-md-12"><label class="small text-secondary mb-1">CITY</label><input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($user['city']); ?>" required></div>
                        </div>
                        <button type="submit" name="update_btn" class="btn mt-4 shadow fw-bold" style="background:var(--netflix-red); color:white; padding:12px 40px;">SAVE CHANGES</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $("#fileInput").change(function(){
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#imgPrev').attr('src', e.target.result); }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>