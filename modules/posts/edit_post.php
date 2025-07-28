<?php

if (!defined('_TanHien')) {
    die('Access denied');
}

if (!isLogin()) {
    redirect('?module=auth&action=login');
}

$msg = '';
$msg_type = '';
$errorArr = [];

// Get post ID from URL
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$postId) {
    setFlashSession('msg', 'Invalid post ID.');
    setFlashSession('msg_type', 'danger');
    redirect('?module=dashboard&action=posts');
}

$userId = getSession('user_id');
$isAdmin = (getSession('permission') === 'admin');

// Get post data and check permissions
$post = getOne("
    SELECT 
        p.post_id,
        p.content,
        p.module_id,
        p.image_path,     
        p.user_id,
        u.username
    FROM posts p
    LEFT JOIN users u ON p.user_id = u.user_id
    WHERE p.post_id = $postId
");

// Check if post exists
if (!$post) {
    setFlashSession('msg', 'Post not found.');
    setFlashSession('msg_type', 'danger');
    redirect('?module=dashboard&action=posts');
}

// Check if user has permission to edit this post
if (!$isAdmin && $post['user_id'] != $userId) {
    setFlashSession('msg', 'You do not have permission to edit this post.');
    setFlashSession('msg_type', 'danger');
    redirect('?module=dashboard&action=posts');
}

// Get all modules from database
$modules = getAll("SELECT module_id, module_name FROM modules ORDER BY module_name");

// Handle form submission
if (isPost()) {
    $filter = filterData();
    $error = [];

    // validate module selection
    if (empty(trim($filter['module_id']))) {
        $error['module_id']['required'] = 'Module selection is required';
    } else {
        // Check if selected module exists
        $moduleExists = getRows("SELECT module_id FROM modules WHERE module_id = " . intval($filter['module_id']));
        if ($moduleExists == 0) {
            $error['module_id']['invalid'] = 'Invalid module selected';
        }
    }

    // validate content
    if (empty(trim($filter['content']))) {
        $error['content']['required'] = 'Content is required';
    } else {
        if (strlen(trim($filter['content'])) < 10) {
            $error['content']['length'] = 'Content must be at least 10 characters';
        }
    }

    // check if no validation errors
    if (empty($error)) {
        $data = [
            'content' => trim($filter['content']),
            'module_id' => intval($filter['module_id']),
            'updated_at' => date('Y-m-d H:i:s') // Add updated timestamp if your table has this field
        ];

        $updateStatus = update('posts', $data, "post_id = $postId");
        
        if ($updateStatus) {
            redirect('?module=dashboard&action=dashboard_posts');
        } else {
            $msg = 'Failed to update post. Please try again.';
            $msg_type = 'danger';
        }

    } else {
        $msg = 'Please fix the errors below!';
        $msg_type = 'danger';
        $errorArr = $error;
    }
}

// Get flash messages if redirected back
if (empty($msg)) {
    $msg = getFlashSession('msg');
    $msg_type = getFlashSession('msg_type');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Post - QUIZZ</title>
  <link rel="stylesheet" href="/php/public/assets/css/create_post.css">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
</head>

<body>
  <div class="container">
    <h1 class="page-title">Edit Post</h1>

    <?php 
    if(!empty($msg) && !empty($msg_type)){
        getMsg($msg, $msg_type);
    }
    $isEdit = true;
    include 'index.php';
    ?>

</body>

</html>