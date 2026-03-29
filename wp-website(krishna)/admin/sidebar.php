<!-- 1. The Sidebar (Navigation) -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <i class="fas fa-play text-white"></i>
        </div>
        <span class="logo-text">FILMFLIX</span>
    </div>
    
    <nav class="nav-group">
        <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="movies.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'movies.php' ? 'active' : '' ?>">
            <i class="fas fa-film"></i>
            <span class="nav-label">Movies/TV Shows</span>
        </a>
        <a href="watchlist.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'watchlist.php' ? 'active' : '' ?>">
            <i class="fas fa-bookmark"></i>
            <span class="nav-label">Watchlist</span>
        </a>
        <a href="categories.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
            <i class="fas fa-list-ul"></i>
            <span class="nav-label">Categories</span>
        </a>
        <a href="actors.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'actors.php' ? 'active' : '' ?>">
            <i class="fas fa-user-tie"></i>
            <span class="nav-label">Actors/Directors</span>
        </a>
        <a href="users.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span class="nav-label">Users</span>
        </a>
        <a href="payments.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i>
            <span class="nav-label">Payments/Subscriptions</span>
        </a>
        <a href="offers.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'offers.php' ? 'active' : '' ?>">
            <i class="fas fa-tag"></i>
            <span class="nav-label">Offers/Promotions</span>
        </a>
        <a href="comments.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : '' ?>">
            <i class="fas fa-comments"></i>
            <span class="nav-label">Comments/Reviews</span>
        </a>
        <a href="banners.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'banners.php' ? 'active' : '' ?>">
            <i class="fas fa-image"></i>
            <span class="nav-label">Promotional Banners</span>
        </a>
        <a href="feedback.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i>
            <span class="nav-label">User Feedback</span>
        </a>
        <a href="settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span class="nav-label">Settings</span>
        </a>
        <a href="setup.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'setup.php' ? 'active' : '' ?>">
            <i class="fas fa-database"></i>
            <span class="nav-label">Database Setup</span>
        </a>
        <a href="https://filmflix-website.com" target="_blank" class="nav-item">
            <i class="fas fa-external-link-alt"></i>
            <span class="nav-label">View Website</span>
        </a>
        <a href="logout.php" class="nav-item" style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 1.5rem; color: #ef4444;">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-label">Logout</span>
        </a>
    </nav>
</aside>
