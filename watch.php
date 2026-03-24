<?php 
session_start();
include_once 'config.php';

// 1. Login Check
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$u_id = $_SESSION['user_id'];

// 2. Fetch User Data for Sidebar
$query = "SELECT * FROM user WHERE id = '$u_id' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

$user_name = $user['fullname']; 
$db_image  = $user['image']; 

// 3. Profile Image Logic
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
    <title>My Watchlist - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --netflix-red: #e50914; --dark-bg: #0c0c0c; --card-bg: #111; }
        
        body { background-color: var(--dark-bg); color: white; font-family: 'Segoe UI', sans-serif; margin: 0; overflow-x: hidden; }
        
        /* Navbar */
        .navbar { 
            position: fixed; top: 0; left: 0; width: 100%; height: 70px; 
            z-index: 1100; background-color: #000; border-bottom: 1px solid #222; 
            padding: 0 25px; 
        }
        .navbar-brand { color: var(--netflix-red) !important; font-weight: 800; font-size: 1.6rem; text-decoration: none; }
        
        /* Sidebar */
        .sidebar { 
            position: fixed; top: 70px; left: 0; width: 260px; 
            height: 100vh; background-color: #000; 
            padding-top: 20px; z-index: 1000; border-right: 1px solid #111;
        }

        /* Sidebar Profile Section (New) */
        .sidebar-user-section { 
            text-align: center; padding: 20px 0; border-bottom: 1px solid #111; margin-bottom: 10px; 
        }
        .sidebar-img { 
            width: 90px; height: 90px; border-radius: 50%; border: 3px solid var(--netflix-red); 
            object-fit: cover; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(229, 9, 20, 0.3);
        }
        .sidebar-name { font-size: 1.1rem; font-weight: 600; color: #fff; margin: 0; }
        .sidebar-status { font-size: 0.7rem; color: #808080; text-transform: uppercase; letter-spacing: 1px; }

        .nav-link { 
            color: #808080; padding: 15px 30px; display: flex; 
            align-items: center; text-decoration: none; font-size: 1rem; transition: 0.3s; 
        }
        .nav-link i { margin-right: 15px; width: 20px; text-align: center; font-size: 1.1rem; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: var(--netflix-red); background: rgba(229, 9, 20, 0.05); border-right: 4px solid var(--netflix-red); }
        
        .sidebar-divider { border-top: 1px solid #222; margin: 15px 0; }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 100px 40px 40px 40px; }

        /* Movie Card Styling */
        .watchlist-card { 
            background: var(--card-bg); border: 1px solid #222; border-radius: 8px; 
            overflow: hidden; transition: 0.4s ease; height: 100%; position: relative;
        }
        .watchlist-card:hover { transform: translateY(-10px); border-color: var(--netflix-red); box-shadow: 0 15px 30px rgba(0,0,0,0.7); }
        
        .poster-container { position: relative; aspect-ratio: 2/3; overflow: hidden; }
        .poster-container img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .watchlist-card:hover .poster-container img { scale: 1.1; filter: brightness(0.7); }

        .rating-badge { position: absolute; top: 10px; left: 10px; background: rgba(229, 9, 20, 0.9); padding: 3px 8px; border-radius: 4px; color: #fff; font-size: 0.75rem; font-weight: bold; z-index: 2; }
        
        .card-info { padding: 15px; text-align: center; }
        .movie-title { font-size: 1rem; font-weight: 700; margin-bottom: 5px; color: #fff; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; }
        .movie-meta { font-size: 0.8rem; color: #777; margin-bottom: 15px; }
        
        .btn-watch { background-color: var(--netflix-red); color: white; border-radius: 4px; width: 100%; font-weight: 700; font-size: 0.8rem; padding: 10px; border: none; transition: 0.3s; }
        .btn-watch:hover { background-color: #b20710; box-shadow: 0 0 15px rgba(229, 9, 20, 0.4); }

        @media (max-width: 992px) {
            .sidebar { width: 80px; }
            .sidebar .nav-link span, .sidebar-user-section { display: none; }
            .main-content { margin-left: 80px; }
            .nav-link { justify-content: center; }
            .nav-link i { margin: 0; }
        }
    </style>
</head>
<body>

    <nav class="navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <a class="navbar-brand" href="dashboard.php">FILMFLIX</a>
            <a href="c.php" class="text-white text-decoration-none d-none d-md-block small fw-bold">HOME</a>
        </div>
        <div class="d-flex align-items-center">
            <span class="text-white me-3 d-none d-md-inline fw-medium"><?php echo htmlspecialchars($user_name); ?></span>
            <img src="<?php echo $profile_img; ?>" class="rounded-circle shadow" width="40" height="40" style="object-fit: cover; border: 1px solid #333;">
        </div>
    </nav>

    <div class="sidebar">
        <div class="sidebar-user-section">
            <img src="<?php echo $profile_img; ?>" class="sidebar-img">
            <h6 class="sidebar-name"><?php echo htmlspecialchars($user_name); ?></h6>
            <span class="sidebar-status">Standard Plan</span>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link active" href="watch.php">
                <i class="fas fa-heart"></i> <span>Watchlist</span>
            </a>
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user-edit"></i> <span>Edit Profile</span>
            </a>
            <a class="nav-link" href="change_password.php">
                <i class="fas fa-lock"></i> <span>Change Password</span>
            </a>
            
            <div class="sidebar-divider"></div>
            
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-5">
            <h2 class="fw-bold m-0 text-uppercase" style="letter-spacing: 2px;">My <span style="color:#e50914;">Watchlist</span></h2>
            <div style="width: 60px; height: 4px; background: #e50914; margin-top: 10px;"></div>
        </div>

        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="watchlist-card">
                    <div class="poster-container">
                        <span class="rating-badge">★ 7.5</span>
                        <img src="https://m.media-amazon.com/images/M/MV5BNDJjMzc4NGYtZmFmNS00Y2RkLWE1Y2MtM2ExYWE1ZWYyNjgzXkEyXkFqcGdeQXVyMTEyMjM2NDc2._V1_.jpg" alt="Movie">
                    </div>
                    <div class="card-info">
                        <h6 class="movie-title">Evil Dead Rise</h6>
                        <p class="movie-meta">2023 • Horror</p>
                        <a href="time.php" class="btn btn-watch">BOOK TICKETS</a>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="watchlist-card">
                    <div class="poster-container">
                        <span class="rating-badge">★ 8.8</span>
                        <img src="https://m.media-amazon.com/images/M/MV5BNjU3N2QxNzYtMjk1NC00MTc0LTk1NTQtMmUxN2IzNjIzOWNlXkEyXkFqcGdeQXVyODgzOTM0ODI@._V1_.jpg" alt="Movie">
                    </div>
                    <div class="card-info">
                        <h6 class="movie-title">Inception</h6>
                        <p class="movie-meta">2010 • Sci-Fi</p>
                        <a href="time.php" class="btn btn-watch">BOOK TICKETS</a>
                    </div>
                </div>
            </div>
            
            </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>