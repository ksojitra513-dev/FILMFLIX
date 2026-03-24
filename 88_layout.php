<?php
// 1. Ekdam top par output buffering ane session chalu karo
ob_start();
@session_start();
include_once("11_Config.php");

// 2. Nav User Data fetching logic (Security fix sathe)
$nav_user_data = null;
if (isset($_SESSION['user'])) {
    $session_email = mysqli_real_escape_string($con, $_SESSION['user']);
    $nav_user_query = "SELECT * FROM user WHERE email='$session_email' LIMIT 1";
    $nav_user_result = mysqli_query($con, $nav_user_query);
    if ($nav_user_result && mysqli_num_rows($nav_user_result) === 1) {
        $nav_user_data = mysqli_fetch_assoc($nav_user_result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'JK Store'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="js/jquery.js"></script>
    <script src="js/validate.js"></script>

    <style>
        /* === COMPACT FOOTER === */
        .site-footer {
            background: #000;
            /* Simple black background for better matching */
            color: #fff;
            padding: 25px 15px 15px;
            /* Padding bohot kam kar di */
            font-family: 'Roboto', sans-serif;
            border-top: 1px solid #1a1a1a;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Sab kuch ek line mein dikhega */
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
        }

        /* Sabhi sections ko chota kiya */
        .footer-logo,
        .footer-contact,
        .footer-social {
            flex: 1 1 200px;
            margin: 10px;
        }

        .footer-logo h2 {
            color: #e50914;
            /* Theme match red */
            font-size: 1.4em;
            /* Size choti ki */
            margin-bottom: 5px;
        }

        .footer-logo p {
            font-size: 0.85em;
            color: #bbb;
        }

        .footer-contact h3,
        .footer-social h3 {
            margin-bottom: 10px;
            font-size: 1em;
            /* Header chota kiya */
            color: #fff;
        }

        .footer-contact p {
            font-size: 0.85em;
            line-height: 1.4;
            margin-bottom: 5px;
            color: #bbb;
        }

        .footer-social a {
            color: #fff;
            font-size: 1.1em;
            /* Icons chote kiye */
            margin-right: 12px;
            transition: 0.3s;
        }

        .footer-social a:hover {
            color: #e50914;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            margin-top: 20px;
            font-size: 0.8em;
            color: #777;
        }





        :root {
            --primary-gradient: linear-gradient(135deg, #141414 0%, #2c2c2c 100%);
            --netflix-red: #E50914;
            --dark-bg: #141414;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--dark-bg);
            color: white;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #333;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--netflix-red) !important;
            letter-spacing: 1px;
        }

        .nav-link {
            color: #e5e5e5 !important;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: var(--netflix-red) !important;
        }

        .nav-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            object-fit: cover;
        }

        /* Alert styling for Cookies */
        .alert-custom {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        main {
            flex: 1;
            padding: 2rem 0;
        }

        footer {
            background: #000;
            color: #808080;
            padding: 2rem 0;
            border-top: 1px solid #333;
        }

        footer a {
            color: #808080;
            text-decoration: none;
            font-size: 14px;
            margin-left: 15px;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#.php"><span style="color:white;">FLIM</span>FLIX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="c2.php">Back to Home</a></li>

                    <?php if (isset($_SESSION['user'])):
                        $display_name = $nav_user_data['fullname'] ?? 'User';
                        $display_picture = $nav_user_data['profile_picture'] ?? 'default.png';
                        $img_path = "images/profile_pictures/" . $display_picture;
                    ?>
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?= $img_path ?>" alt="Profile" class="nav-user-avatar">
                                <span><?= htmlspecialchars($display_name) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary">
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="edit_profile.php">Settings</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Sign Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-success btn-sm px-4" href="login22.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="alert-custom">
        <?php if (isset($_COOKIE['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= $_COOKIE['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_COOKIE['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= $_COOKIE['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <main>
        <div class="container">
            <?php echo isset($content) ? $content : '<p class="text-center">No content available.</p>'; ?>
        </div>
    </main>


    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-logo">
                <h2>🎬 FILMFLIX</h2>
                <p>Book movies anytime, anywhere.</p>
            </div>

            <div class="footer-contact">
                <h3>Contact</h3>
                <p><i class="fas fa-phone-alt"></i> +91 12345 67890</p>
                <p><i class="fas fa-envelope"></i> help@filmflix.com</p>
            </div>

            <div class="footer-social">
                <h3>Follow Us</h3>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> FILMFLIX. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alerts automatic hide karva mate (5 sec)
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    </script>
</body>

</html>