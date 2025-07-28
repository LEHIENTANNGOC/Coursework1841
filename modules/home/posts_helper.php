<?php
// posts_helper.php
if (!defined('_TanHien')) {
    die('Access denied');
}

/**
 * Lấy thông tin like và comment cho các posts
 */
function getPostsWithLikesAndComments($posts) {
    $user_id = getSession('user_id');
    
    foreach ($posts as $index => $post) {
        // Đếm số like
        $likeCount = getOne("SELECT COUNT(*) as count FROM post_likes WHERE post_id = " . $post['post_id']);
        $posts[$index]['like_count'] = $likeCount['count'] ?? 0;
        
        // Kiểm tra user hiện tại đã like chưa
        $userLiked = false;
        if ($user_id) {
            $userLike = getOne("SELECT * FROM post_likes WHERE post_id = " . $post['post_id'] . " AND user_id = $user_id");
            $userLiked = !empty($userLike);
        }
        $posts[$index]['user_liked'] = $userLiked;
        
        // Lấy comments
        $comments = getAll("
            SELECT c.*, u.username 
            FROM post_comments c 
            LEFT JOIN users u ON c.user_id = u.user_id 
            WHERE c.post_id = " . $post['post_id'] . " 
            ORDER BY c.created_at ASC
        ");
        $posts[$index]['comments'] = $comments;
        $posts[$index]['comment_count'] = count($comments);
    }
    
    return $posts;
}

/**
 * Format thời gian hiển thị
 */
function formatTimeAgo($datetime) {
    $postTime = strtotime($datetime);
    $now = time();
    $diff = $now - $postTime;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . 'm ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . 'h ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . 'd ago';
    } else {  
        return date('M j, Y', $postTime);
    }
}

/**
 * Hiển thị thông báo flash message
 */
function displayFlashMessage() {
    $msg = getFlashSession('msg');
    $msg_type = getFlashSession('msg_type') ?: 'success';
    
    if ($msg) {
        echo '<div class="message ' . $msg_type . '">' . htmlspecialchars($msg) . '</div>';
    }
}

/**

 * Lấy thông tin user hiện tại
 */
function getCurrentUser() {
    return [
        'user_id' => getSession('user_id'),
        'username' => getSession('username'),
        'email' => getSession('email'),
        'permission' => getSession('permission')
    ];
}