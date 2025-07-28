<?php
// posts_display.php
if (!defined('_TanHien')) {
    die('Access denied');
}

// Include các file helper
require_once 'posts_helper.php';
require_once 'like_handler.php';
require_once 'comment_handler.php';

// Lấy thông tin toggle comment từ URL
$toggle_post_id = $_GET['toggle_comments'] ?? null;

// Get all posts with search functionality
$keyword = $_GET['q'] ?? '';

if (!empty($keyword)) {
    $posts = searchPosts($keyword);
} else {
    $posts = getAll("
        SELECT
            p.post_id,
            p.content,
            p.image_path,
            p.created_at,
            u.username,
            m.module_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.user_id
        LEFT JOIN modules m ON p.module_id = m.module_id
        ORDER BY p.created_at DESC
    ");
}

// Lấy thông tin like và comment cho các posts
$posts = getPostsWithLikesAndComments($posts);

// Lấy thông tin user hiện tại
$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/php/public/assets/css/posts_display.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <title>Posts - Social Platform</title>
</head>

<body>

  <div class="posts-container">
    <?php displayFlashMessage(); ?>

    <h2 class="posts-title">Recent Posts</h2>

    <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
    <div class="post-card">
      <!-- POST HEADER -->
      <div class="post-header">
        <div class="post-author">
          <div class="author-icon">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
              </path>
            </svg>
          </div>
          <div class="author-info">
            <span class="author-name"><?php echo htmlspecialchars($post['username'] ?? 'Unknown User'); ?></span>
            <span class="post-module"><?php echo htmlspecialchars($post['module_name'] ?? 'General'); ?></span>
          </div>
        </div>
        <div class="post-time">
          <?php echo formatTimeAgo($post['created_at']); ?>
        </div>
      </div>

      <!-- POST IMAGE -->
      <?php if (!empty($post['image_path'])): ?>
      <div class="post-image">
        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Cover" class="cover-image"
          onerror="this.style.display='none'">
      </div>
      <?php endif; ?>

      <!-- POST CONTENT -->
      <div class="post-content">
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
      </div>

      <!-- POST ACTIONS (LIKE & COMMENT BUTTONS) -->
      <div class="post-actions">
        <div class="post-actions-buttons">
          <!-- Like Button -->
          <?php if ($currentUser['user_id']): ?>
          <form method="POST" style="display: inline-block;">
            <input type="hidden" name="action" value="like">
            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
            <button type="submit" class="action-btn like-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>">
              <i class="fa <?php echo $post['user_liked'] ? 'fa-heart' : 'fa-heart-o'; ?>" aria-hidden="true"></i>
              <span><?php echo $post['like_count']; ?> Like<?php echo $post['like_count'] != 1 ? 's' : ''; ?></span>
            </button>
          </form>
          <?php else: ?>
          <span class="action-btn like-btn disabled">
            <i class="fa fa-heart-o" aria-hidden="true"></i>
            <span><?php echo $post['like_count']; ?> Like<?php echo $post['like_count'] != 1 ? 's' : ''; ?></span>
          </span>
          <?php endif; ?>

          <!-- Comment Toggle Button -->
          <button type="button" class="action-btn comment-btn"
            onclick="toggleComments(<?php echo $post['post_id']; ?>)">
            <i class="fa fa-comment-o" aria-hidden="true"></i>
            <span>
              <?php echo $post['comment_count']; ?>
              Comment<?php echo $post['comment_count'] != 1 ? 's' : ''; ?>
            </span>
          </button>
        </div>

        <!-- COMMENTS SECTION -->
        <div class="comments-section" id="comments-<?php echo $post['post_id']; ?>" style="display: none;">
          <!-- Comment Form -->
          <?php if ($currentUser['user_id']): ?>
          <div class="comment-form">
            <form method="POST">
              <input type="hidden" name="action" value="comment">
              <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
              <textarea name="comment_content" placeholder="Viết comment của bạn..." class="comment-input" required
                maxlength="1000"></textarea>
              <button type="submit" class="btn-primary btn-comment">Đăng Comment</button>
            </form>
          </div>
          <?php else: ?>
          <div class="comment-form">
            <p style="text-align: center; color: #666; padding: 20px;">
              <a href="?module=auth&action=login">Đăng nhập</a> để comment
            </p>
          </div>
          <?php endif; ?>

          <!-- Display Comments -->
          <?php if (!empty($post['comments'])): ?>
          <div class="comments-list">
            <?php foreach ($post['comments'] as $comment): ?>
            <div class="comment-item">
              <div class="comment-header">
                <div class="comment-author">
                  <div class="comment-author-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                        clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <span
                    class="comment-author-name"><?php echo htmlspecialchars($comment['username'] ?? 'Unknown User'); ?></span>
                </div>
                <div class="comment-time">
                  <?php echo formatTimeAgo($comment['created_at']); ?>
                </div>
              </div>
              <div class="comment-content">
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="no-comments">
            <p style="text-align: center; color: #666; padding: 20px;">Chưa có comment nào. Hãy là người đầu tiên
              comment!
            </p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div> <!-- ĐÂY LÀ THẺ ĐÓNG THIẾU CHO POST-CARD -->
    <?php endforeach; ?>
    <?php else: ?>
    <div class="no-posts">
      <div class="no-posts-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
          </path>
        </svg>
      </div>
      <h3>Chưa có bài viết nào</h3>
      <p>Hãy là người đầu tiên chia sẻ với cộng đồng!</p>
      <a href="?module=posts&action=create_post" class="btn-primary btn-create-first">Tạo bài viết đầu tiên</a>
    </div>
    <?php endif; ?>
  </div> <!-- ĐÂY LÀ THẺ ĐÓNG CHO POSTS-CONTAINER -->

  <script>
  function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    const commentBtn = event.target.closest('.comment-btn');

    if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
      commentsSection.style.display = 'block';
      commentBtn.classList.add('active');
    } else {
      commentsSection.style.display = 'none';
      commentBtn.classList.remove('active');
    }
  }

  // Tự động mở comment section nếu vừa comment
  <?php if (isset($_GET['show_comments'])): ?>
  document.addEventListener('DOMContentLoaded', function() {
    const postId = <?php echo $_GET['show_comments']; ?>;
    const commentsSection = document.getElementById('comments-' + postId);
    const commentBtn = document.querySelector('[onclick="toggleComments(' + postId + ')"]');

    if (commentsSection) {
      commentsSection.style.display = 'block';
      if (commentBtn) {
        commentBtn.classList.add('active');
      }
    }
  });
  <?php endif; ?>
  </script>

</body>

</html>