<?php

if (!defined('_TanHien')) {
    die('Access denied');
}

// Check if user is logged in
if (!isLogin()) {
    redirect('?module=auth&action=login');
}

$msg = '';
$msg_type = '';
$errorArr = [];

$modules = getAll("SELECT module_id, module_name FROM modules ORDER BY module_name");

if (isPost()) {
    $filter = filterData();
    $error = [];

    // Validate module_id
    if (empty(trim($filter['module_id']))) {
        $error['module_id']['required'] = 'Module selection is required';
    } else {
        $moduleExists = getRows("SELECT module_id FROM modules WHERE module_id = " . intval($filter['module_id']));
        if ($moduleExists == 0) {
            $error['module_id']['invalid'] = 'Invalid module selected';
        }
    }

    // Validate content
    if (empty(trim($filter['content']))) {
        $error['content']['required'] = 'Content is required';
    } else {
        if (strlen(trim($filter['content'])) < 10) {
            $error['content']['length'] = 'Content must be at least 10 characters';
        }
    }

    $imagePath = ''; 
    $errorArr = [];

    include_once 'handle_upload_image.php'; // upload file xử lý

    // Nếu không có lỗi -> lưu DB
    if (empty($error)) {
        $data = [
            'title' => '',
            'content' => trim($filter['content']),
            'image_path' => $imagePath,
            'user_id' => getSession('user_id'),
            'module_id' => intval($filter['module_id']),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insertStatus = insert('posts', $data);

        if ($insertStatus) {
            redirect('?module=home&action=home_auth');
        } else {
            $msg = 'Failed to create post. Please try again.';
            $msg_type = 'danger';
        }
    } else {
        $errorArr = $error;
    }
}

if (empty($msg)) {
    $msg = getFlashSession('msg');
    $msg_type = getFlashSession('msg_type');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create New Post - QUIZZ</title>
  <link rel="stylesheet" href="/php/public/assets/css/create_post.css">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
</head>

<body>
  <div class="container">
    <h1 class="page-title">Create New Post</h1>

    <?php 
    if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }

    $isEdit = false;
    include 'index.php';
    ?>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const addCoverBtn = document.querySelector('.add-cover-btn');
    const fileInput = document.getElementById('cover_image');
    const imagePreview = document.getElementById('image-preview');
    const removeBtn = document.getElementById('remove-image');

    addCoverBtn.addEventListener('click', function() {
      fileInput.click();
    });

    fileInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          imagePreview.innerHTML = `<img src="${e.target.result}" alt="Cover Preview">`;
          imagePreview.style.display = 'block';
          removeBtn.style.display = 'inline-block';
          addCoverBtn.style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });

    removeBtn.addEventListener('click', function() {
      fileInput.value = '';
      imagePreview.style.display = 'none';
      removeBtn.style.display = 'none';
      addCoverBtn.style.display = 'flex';
      imagePreview.innerHTML = '';
    });
  });
  </script>
</body>

</html>