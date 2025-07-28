<?php
// dashboard_posts.php
if (!defined('_TanHien')) {
    die('Access denied');
}

if (!isLogin()) {
    redirect('?module=auth&action=login');
}

$msg = '';
$msg_type = '';

// Handle delete post
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $userId = getSession('user_id');
    
    // Check if post belongs to current user or user is admin
    $checkPost = getOne("SELECT user_id FROM posts WHERE post_id = $deleteId");
    
    if ($checkPost && ($checkPost['user_id'] == $userId || getSession('permission') === 'admin')) {
        $deleteStatus = delete('posts', "post_id = $deleteId");
        
        if ($deleteStatus->rowCount() > 0) {
        redirect('?module=dashboard&action=dashboard_posts');

        } else {
            setFlashSession('msg', 'Failed to delete post.');
            setFlashSession('msg_type', 'danger');
        }
    } else {
        setFlashSession('msg', 'You do not have permission to delete this post.');
        setFlashSession('msg_type', 'danger');
    }
    
    redirect('?module=dashboard&action=dashboard_posts');
}

// Get flash messages
if (empty($msg)) {
    $msg = getFlashSession('msg');
    $msg_type = getFlashSession('msg_type');
}

// Get posts for current user or all posts if admin
$userId = getSession('user_id');
$isAdmin = (getSession('permission') === 'admin');

if ($isAdmin) {
    $posts = getAll("
        SELECT 
            p.post_id,
            p.content,
            p.created_at,
            u.username,
            m.module_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.user_id
        LEFT JOIN modules m ON p.module_id = m.module_id
        ORDER BY p.created_at DESC
    ");
} else {
    $posts = getAll("
        SELECT 
            p.post_id,
            p.content,
            p.created_at,
            u.username,
            m.module_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.user_id
        LEFT JOIN modules m ON p.module_id = m.module_id
        WHERE p.user_id = $userId
        ORDER BY p.created_at DESC
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
      <h2>My Posts Dashboard</h2>
      <a href="?module=posts&action=create_post" class="btn btn-primary">Create New Post</a>
    </div>

    <?php 
        if (!empty($msg) && !empty($msg_type)) {
            getMsg($msg, $msg_type);
        }
        ?>

    <div class="posts-list">
      <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?>
      <div class="post-item">
        <div class="post-info">
          <h3 class="post-title">
            <?php 
                                $title = substr(strip_tags($post['content']), 0, 50);
                                echo htmlspecialchars($title) . (strlen($post['content']) > 50 ? '...' : '');
                                ?>
          </h3>
          <div class="post-meta">
            <span class="post-author"><?php echo htmlspecialchars($post['username'] ?? 'Unknown'); ?></span>
            <span class="post-date">Published: <?php echo date('M j', strtotime($post['created_at'])); ?></span>
            <?php if (!empty($post['module_name'])): ?>
            <span class="post-module">Module: <?php echo htmlspecialchars($post['module_name']); ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="post-stats">
          <span class="stat-item">👁 < 25</span>
        </div>

        <div class="post-actions">
          <a href="?module=posts&action=edit_post&id=<?php echo $post['post_id']; ?>"
            class="btn-action btn-edit">Edit</a>
          <a href="?module=dashboard&action=dashboard_posts&delete_id=<?php echo $post['post_id']; ?>"
            class="btn-action btn-delete">Delete</a>
        </div>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
      <div class="no-posts-dashboard">
        <h3>No posts yet</h3>
        <p>You haven't created any posts yet. Start sharing your thoughts with the community!</p>
        <a href="?module=posts&action=create_post" class="btn btn-primary">Create Your First Post</a>
      </div>
      <?php endif; ?>
    </div>


  </div>



</body>

</html>