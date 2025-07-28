<?php
// like_handler.php
if (!defined('_TanHien')) {
    die('Access denied');
}

// Xử lý like action
if (isPost() && isset($_POST['action']) && $_POST['action'] == 'like') {
    $post_id = $_POST['post_id'];
    $user_id = getSession('user_id');
    
    if ($user_id && !empty($post_id)) {
        // Kiểm tra đã like chưa
        $existingLike = getOne("SELECT * FROM post_likes WHERE post_id = $post_id AND user_id = $user_id");
        
        if ($existingLike) {
            // Nếu đã like thì bỏ like
            delete('post_likes', "post_id = $post_id AND user_id = $user_id");
            setFlashSession('msg', 'Đã bỏ like bài viết!');
            setFlashSession('msg_type', 'success');
        } else {
            // Nếu chưa like thì thêm like
            $likeData = [
                'post_id' => $post_id,
                'user_id' => $user_id
            ];
            
            if (insert('post_likes', $likeData)) {
                setFlashSession('msg', 'Đã like bài viết!');
                setFlashSession('msg_type', 'success');
            } else {
                setFlashSession('msg', 'Có lỗi xảy ra khi like bài viết!');
                setFlashSession('msg_type', 'error');
            }
        }
    }
    

    redirect("?module=$module&action=$action");
}
?>