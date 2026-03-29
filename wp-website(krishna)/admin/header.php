<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX Admin | Dynamic Dashboard</title>
    <meta name="description" content="Manage your FilmFlix platform with a powerful, dynamic admin dashboard and real-time site health analytics.">
    <link rel="stylesheet" href="style.css">
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for site health overview -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Main Content Area -->
        <main class="main-content">
            <header>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button id="sidebar-toggle" class="mobile-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search movies, users, or transactions...">
                    </div>
                </div>
                <div class="header-actions">
                    <div class="notification-bell">
                        <i class="far fa-bell" style="font-size: 1.25rem; cursor: pointer; color: var(--text-muted);"></i>
                    </div>
                    <?php 
                        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'Admin' ORDER BY id ASC LIMIT 1");
                        $admin = $stmt->fetch();
                        $adminName = $admin['name'] ?? 'Admin User';
                        $adminRole = $admin['role'] ?? 'Super Admin';
                        $adminImg = (!empty($admin['img']) && !str_starts_with($admin['img'], 'http')) ? 'uploads/'.$admin['img'] : 'https://ui-avatars.com/api/?name='.urlencode($adminName).'&background=6366f1&color=fff';
                    ?>
                    <a href="profile.php" class="user-profile" style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; text-decoration: none;">
                        <img src="<?= $adminImg ?>" alt="User Profile" class="user-avatar" id="profile-avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--border-color); object-fit: cover;">
                        <div class="user-info">
                            <p style="font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin: 0;"><?= htmlspecialchars($adminName) ?></p>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;"><?= htmlspecialchars($adminRole) ?></p>
                        </div>
                    </a>
                </div>
            </header>
            <div class="fade-in">
