<?php
// dashboard_modules.php
if (!defined('_TanHien')) {
    die('Access denied');
}

if (!isLogin()) {
    redirect('?module=auth&action=login');
}

$msg = '';
$msg_type = '';

// Handle delete module
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    
    // Delete all posts in this module first
    delete('posts', "module_id = $deleteId");
    
    // Then delete the module
    $deleteStatus = delete('modules', "module_id = $deleteId");
    
    if ($deleteStatus->rowCount() > 0) {
        redirect('?module=dashboard&action=dashboard_module');

    } else {
        setFlashSession('msg', 'Failed to delete module.');
        setFlashSession('msg_type', 'danger');
    }
    
    redirect('?module=dashboard&action=dashboard_module');
}

// Get flash messages
if (empty($msg)) {
    $msg = getFlashSession('msg');
    $msg_type = getFlashSession('msg_type');
}

// Get modules for current user or all modules if admin
$userId = getSession('user_id');
$isAdmin = (getSession('permission') === 'admin');

if ($isAdmin) {
    $modules = getAll("
        SELECT 
            m.module_id,
            m.module_name,
            m.created_at,
            u.username,
            COUNT(p.post_id) as post_count
        FROM modules m
        LEFT JOIN users u ON m.user_id = u.user_id
        LEFT JOIN posts p ON m.module_id = p.module_id
        GROUP BY m.module_id, m.module_name, m.created_at, u.username
        ORDER BY m.created_at DESC
    ");
} else {
    $modules = getAll("
        SELECT 
            m.module_id,
            m.module_name,
            m.created_at,
            u.username,
            COUNT(p.post_id) as post_count
        FROM modules m
        LEFT JOIN users u ON m.user_id = u.user_id
        LEFT JOIN posts p ON m.module_id = p.module_id
        WHERE m.user_id = $userId
        GROUP BY m.module_id, m.module_name, m.created_at, u.username
        ORDER BY m.created_at DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - QUIZZ</title>
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/home_auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/dashboard_posts.css">
  <link rel="stylesheet" href="/php/public/assets/css/header.css">

  <script src="/php/public/assets/js/home_auth.js"></script>
</head>

<body>
  <?php include_once 'public/assets/header.php'; ?>

  <div class="dashboard-container">
    <div class="dashboard-header">
      <h2>My Modules Dashboard</h2>
      <a href="?module=modules&action=create_module" class="btn btn-primary">Create New Module</a>
    </div>

    <?php 
        if (!empty($msg) && !empty($msg_type)) {
            getMsg($msg, $msg_type);
        }
        ?>

    <div class="posts-list">
      <?php if (!empty($modules)): ?>
      <?php foreach ($modules as $module): ?>
      <div class="post-item">
        <div class="post-info">
          <h3 class="post-title">
            <?php echo htmlspecialchars($module['module_name']); ?>
          </h3>
          <div class="post-meta">
            <span class="post-author"><?php echo htmlspecialchars($module['username'] ?? 'Unknown'); ?></span>
            <span class="post-date">Created: <?php echo date('M j', strtotime($module['created_at'])); ?></span>
            <span class="post-module">ID: <?php echo $module['module_id']; ?></span>
          </div>
        </div>

        <div class="post-stats">
          <span class="stat-item">📝 <?php echo $module['post_count']; ?> posts</span>
        </div>

        <div class="post-actions">
          <a href="?module=dashboard&action=dashboard_module&delete_id=<?php echo $module['module_id']; ?>"
            class="btn-action btn-delete">Delete</a>
        </div>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
      <div class="no-posts-dashboard">
        <h3>No modules yet</h3>
        <p>You haven't created any modules yet. Create modules to organize posts by topics!</p>
        <a href="?module=modules&action=create_module" class="btn btn-primary">Create Your First Module</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>