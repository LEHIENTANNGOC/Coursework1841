<?php
if (!defined('_TanHien')) {
    die('Access denied');
}

if(!isLogin()){
  redirect('?module=auth&action=login');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/php/public/assets/css/home_auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/posts_display.css">
  <link rel="stylesheet" href="/php/public/assets/css/header.css">
  <link rel="stylesheet" href="/php/public/assets/css/contact.css">

  <script src="/php/public/assets/js/home_auth.js"></script>
</head>

<body>
  <?php include_once 'public/assets/header.php'; ?>

  <?php include_once 'posts_display.php'; ?>


  <a href="/php/?module=home&action=contact" class="help-icon" title="Need Help?">
    <i class="fa fa-question-circle" aria-hidden="true"></i>
  </a>

</body>

</html>