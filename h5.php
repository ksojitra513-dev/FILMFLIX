<?php 
// 1. Session check: Agar pehle se start nahi hai toh start karein
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Database connection include karein
include_once 'config.php'; 

// 3. Default Values (Taaki error na aaye)
$user_name = "Guest";
$profile_img = "https://ui-avatars.com/api/?name=Guest&background=e50914&color=fff";
$is_logged_in = false;

// 4. Agar user logged in hai, toh uska data fetch karein
if(isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $u_id = mysqli_real_escape_string($con, $u_id); // Safety check
    
    $query = "SELECT fullname, image FROM user WHERE id = '$u_id' LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        $user_name = $user_data['fullname'];
        $is_logged_in = true;

        // Image Logic
        if(!empty($user_data['image']) && file_exists("uploads/" . $user_data['image'])) {
            $profile_img = "uploads/" . $user_data['image'];
        } else {
            $profile_img = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=e50914&color=fff";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - Premium Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .navbar {
            background-color: #000000;
            padding: 10px 0;
            border-bottom: 2px solid #e50914;
            z-index: 1050 !important;
        }

        .navbar-brand {
            font-size: 28px;
            color: #ffffff;
            text-transform: uppercase;
            text-decoration: none;
            transition: 0.3s;
        }

        .navbar-brand span {
            color: #e50914;
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.5);
        }

        .navbar-nav .nav-link {
            color: #cccccc !important;
            font-weight: 500;
            margin: 0 8px;
            padding: 8px 15px !important;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(229, 9, 20, 0.1);
            border-radius: 5px;
        }

        .navbar-nav .nav-link.active {
            color: #e50914 !important;
            border-bottom: 2px solid #e50914;
        }

        /* User Profile Section */
        .user-name-text {
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .profile-img-nav {
            border: 2px solid #444;
            transition: 0.3s;
            object-fit: cover;
        }

        .profile-img-nav:hover {
            border-color: #e50914;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="c.php">
                FILM<span>FLIX</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="c.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallary.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    
                    <?php if(!$is_logged_in): ?>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link text-info" href="dashboard.php">My Dashboard</a></li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if($is_logged_in): ?>
                        <span class="user-name-text d-none d-md-block"><?php echo htmlspecialchars($user_name); ?></span>
                        <div class="dropdown">
                            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?php echo $profile_img; ?>" class="rounded-circle profile-img-nav" width="40" height="40">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary shadow mt-2">
                                <li><a class="dropdown-item text-white" href="profile.php"><i class="fas fa-user-edit me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item text-white" href="change_password.php"><i class="fas fa-key me-2"></i> Password</a></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-sm btn-outline-danger">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>          
        </div>
    </nav>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary" style="border-radius: 15px;">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold">Logout?</h4>
                    <p class="text-secondary">Are you sure you want to leave <span class="text-white">FILMFLIX</span>?</p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-danger px-4" style="background-color: #e50914;">Yes, Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>