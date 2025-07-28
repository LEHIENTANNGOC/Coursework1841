<?php
// handle_upload_image.php

$imagePath = $imagePath ?? ''; // giữ ảnh cũ nếu chưa có giá trị

if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($_FILES['cover_image']['type'], $allowedTypes)) {
        $errorArr['cover_image']['type'] = 'Only JPG, PNG, GIF and WebP images are allowed';
    } elseif ($_FILES['cover_image']['size'] > $maxSize) {
        $errorArr['cover_image']['size'] = 'Image must be less than 5MB';
    } else {
        $uploadDir = 'public/uploads/posts/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
            $imagePath = $uploadPath;
        } else {
            $errorArr['cover_image']['upload'] = 'Failed to upload image';
        }
    }
}