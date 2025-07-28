<?php
if (!defined('_TanHien')) {
    die('Access denied');
}

removeSession();

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
  <link rel="stylesheet" href="/php/public/assets/css/quiz_sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
  <header class="main-header">
    <div class="header-container">
      <div class="dev-logo">
        QUIZZ
      </div>
      <div class="search-container">
        <input type="text" class="search-input" placeholder="Search...">
      </div>
      <div class="auth-buttons">
        <a href="/php/?module=auth&action=login" class="btn-auth btn-login">Log in</a>
        <a href="/php/?module=auth&action=register" class="btn-auth btn-register">Create account</a>
      </div>
    </div>
  </header>


  <!-- QUIZ COMMUNITY SIDEBAR -->
  <div class="quiz-community-sidebar">
    <h1 class="community-title">
      QUIZ Community is a community of 3,361,135 amazing developers
    </h1>
    <p class="community-subtitle">
      We're a place where coders share, stay up-to-date and grow their careers.
    </p>
    <div class="community-buttons">
      <a href="/php/?module=auth&action=register" class="btn-community btn-create-account">
        Create account
      </a>
      <a href="/php/?module=auth&action=login" class="btn-community btn-login">
        Log in
      </a>
    </div>
  </div>


  <?php include_once 'posts_display.php'; ?>

  <a href="/php/?module=home&action=contact" class="help-icon" title="Need Help?">
    <i class="fa fa-question-circle" aria-hidden="true"></i>
  </a>

</body>

</html>