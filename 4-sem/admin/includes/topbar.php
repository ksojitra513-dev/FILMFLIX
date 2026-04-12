<header class="topbar">
  <div class="topbar-left">
    <button class="menu-toggle" onclick="toggleSidebar()" id="menuToggle">
      <i class="fas fa-bars"></i>
    </button>
    <div class="breadcrumb">
      <span class="breadcrumb-item"><i class="fas fa-home"></i></span>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item active"><?= ucfirst(str_replace(['.php','-'], ['', ' '], basename($_SERVER['PHP_SELF']))) ?></span>
    </div>
  </div>
  <div class="topbar-right">
    <div class="topbar-time" id="topbarTime"></div>
    <a href="profile.php" class="admin-profile" style="text-decoration: none; color: inherit;">
      <div class="admin-avatar">
        <?php if (!empty($_SESSION['admin_avatar'])): ?>
            <img src="../uploads/<?= $_SESSION['admin_avatar'] ?>" class="avatar-img" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
        <?php else: ?>
            <i class="fas fa-user-shield"></i>
        <?php endif; ?>
      </div>
      <div class="admin-details">
        <span class="admin-name"><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
        <span class="admin-role">Super Admin</span>
      </div>
    </a>
  </div>
</header>
<script>
  function updateTime() {
    const now = new Date();
    document.getElementById('topbarTime').textContent = now.toLocaleTimeString('en-IN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
  }
  updateTime(); setInterval(updateTime, 1000);
</script>
