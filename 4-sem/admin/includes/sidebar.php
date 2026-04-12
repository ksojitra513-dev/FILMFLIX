<?php
$current_page = basename($_SERVER['PHP_SELF']);
function nav_item($href, $icon, $label, $current) {
    $current_uri = basename($_SERVER['REQUEST_URI']);
    // Fall back to PHP_SELF if there's no query string
    if (strpos($current_uri, '?') === false && strpos($href, '?') === false) {
        $active = ($current === basename($href)) ? 'active' : '';
    } else {
        // Special case for Categories: Keep active if on categories.php even with different types
        if (basename($href, '.php') === 'categories' && $current === 'categories.php') {
            $active = 'active';
        } else {
            $active = ($current_uri === $href) ? 'active' : '';
        }
    }
    echo "<a href='$href' class='nav-item $active'><i class='fas $icon'></i><span>$label</span></a>";
}
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fas fa-film"></i></div>
    <div class="logo-text">
      <span class="logo-brand">FilmFlix</span>
      <span class="logo-sub">Admin Panel</span>
    </div>
  </div>

  <div class="sidebar-section-label">MAIN</div>
  <nav class="sidebar-nav">
    <?php nav_item('dashboard.php', 'fa-tachometer-alt', 'Dashboard', $current_page); ?>
    <?php nav_item('users.php', 'fa-users', 'Users', $current_page); ?>
    <?php nav_item('bookings.php', 'fa-ticket-alt', 'Bookings', $current_page); ?>
  </nav>

  <div class="sidebar-section-label">CONTENT</div>
  <nav class="sidebar-nav">
    <?php nav_item('movies.php', 'fa-film', 'Movies', $current_page); ?>
    <?php nav_item('theaters.php', 'fa-building', 'Theaters', $current_page); ?>
    <?php nav_item('screens.php', 'fa-desktop', 'Screens', $current_page); ?>
    <?php nav_item('gallery.php', 'fa-images', 'Gallery', $current_page); ?>
    <?php nav_item('offers.php', 'fa-tags', 'Offers', $current_page); ?>
    <?php nav_item('banner.php', 'fa-image', 'Banners', $current_page); ?>
  </nav>

  <div class="sidebar-section-label">CATEGORIES</div>
  <nav class="sidebar-nav">
    <?php nav_item('categories.php', 'fa-layer-group', 'Movie Categories', $current_page); ?>
  </nav>

  <div class="sidebar-section-label">ENGAGEMENT</div>
  <nav class="sidebar-nav">
    <?php nav_item('feedback.php', 'fa-star', 'Feedback', $current_page); ?>
    <?php nav_item('contacts.php', 'fa-envelope', 'Contacts', $current_page); ?>
    <?php nav_item('about.php', 'fa-info-circle', 'About Content', $current_page); ?>
  </nav>

  <div class="sidebar-section-label">SYSTEM</div>
  <nav class="sidebar-nav">
    <?php nav_item('settings.php', 'fa-cog', 'Settings', $current_page); ?>
    <a href="logout.php" class="nav-item text-danger" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
  </nav>

  <div class="sidebar-footer">
    <a href="../index.php" class="nav-item" target="_blank"><i class="fas fa-external-link-alt"></i><span>View Site</span></a>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
