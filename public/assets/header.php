<?php
if (!defined('_TanHien')) {
    die('Access denied');
}
?>

<header class="main-header">
  <div class="header-container">
    <a class="dev-logo" href="/php/?module=home&action=home_auth">
      QUIZZ
    </a>

    <div class="search-container">
      <form method="GET" action="/php/">
        <input type="hidden" name="module" value="home">
        <input type="hidden" name="action" value="home_auth">
        <input type="text" class="search-input" name="q" placeholder="Search...">
      </form>
    </div>

    <div class="auth-buttons">
      <a href="?module=posts&action=create_post" class="btn-create-post">Create Post</a>

      <?php if (isset($_SESSION['permission']) && $_SESSION['permission'] === 'admin'): ?>
      <a href="?module=dashboard&action=dashboard_module" class="btn-create-post"> Module</a>
      <?php endif; ?>

      <div class="user-menu">
        <div class="user-icon" onclick="toggleDropdown()">
          <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
            </path>
          </svg>
        </div>
        <div class="dropdown-menu" id="userDropdown">

          <div class="dropdown-user-info">
            <strong><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></strong><br>
            <small><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></small>
          </div>

          <hr>

          <a href="?module=dashboard&action=dashboard_posts" class="dropdown-item">Dashboard</a>
          <a href="?module=posts&action=create_post" class="dropdown-item">Create Post</a>
          <a href="?module=auth&action=logout" class="dropdown-item">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>