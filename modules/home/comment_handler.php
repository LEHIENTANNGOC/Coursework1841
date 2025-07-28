<?php
// comment_handler.php
if (!defined('_TanHien')) {
    die('Access denied');
}

// Xử lý comment action
if (isPost() && isset($_POST['action']) && $_POST['action'] == 'comment') {
    $post_id = $_POST['post_id'];
    $comment_content = trim($_POST['comment_content']);
    $user_id = getSession('user_id');
    
    if ($user_id && !empty($comment_content) && !empty($post_id)) {
        // Validate comment content
        if (strlen($comment_content) < 1) {
            setFlashSession('msg', 'Comment không được để trống!');
            setFlashSession('msg_type', 'error');
        } elseif (strlen($comment_content) > 1000) {
            setFlashSession('msg', 'Comment không được quá 1000 ký tự!');
            setFlashSession('msg_type', 'error');
        } else {
            // Insert comment
            $commentData = [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'content' => $comment_content
            ];
            
            if (insert('post_comments', $commentData)) {
                setFlashSession('msg', 'Đã thêm comment thành công!');
                setFlashSession('msg_type', 'success');
            } else {
                setFlashSession('msg', 'Có lỗi xảy ra khi thêm comment!');
                setFlashSession('msg_type', 'error');
            }
        }
    } else {
        setFlashSession('msg', 'Thông tin không hợp lệ!');
        setFlashSession('msg_type', 'error');
    }
    
    // Redirect về trang hiện tại và mở comment section

    redirect("?module=$module&action=$action");
}
?>