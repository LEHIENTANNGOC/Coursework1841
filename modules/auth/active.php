<?php

//check active_token co giong token o url ko
//update status từ 0 -> 1; xóa token đi

if (!defined('_TanHien')) {
    die('Access denied');
}

$filter = filterData('get');
// GOOD LINK
if(!empty($filter['token'])){
    $token = $filter['token'];
    $checkToken = getOne("SELECT * FROM users WHERE active_token = '$token'");
    
    if (!empty($checkToken)) {
        // Cập nhật status = 1 và xóa token
        $updateData = [
            'status' => 1,
            'active_token' => null, // hoặc '' nếu muốn set empty string
            'created_at' => date('Y-m-d H:i:s')
            
        ];
        
        $condition = "user_id = " . $checkToken['user_id'];
        update('users', $updateData, $condition);
        
        // Hiển thị thông báo thành công
        ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Account Activated</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/active.css">
</head>

<body>
  <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
    <div class="form-box">
      <div class="success-icon"></div>
      <h2>Account Activated Successfully!</h2>
      <p class="success-message">
        Your account has been activated successfully. You can now log in and use all system features.
      </p>
      <div class="d-grid">
        <a href="/php/?module=auth&action=login" class="btn btn-primary">Login Now</a>
      </div>
      <p class="additional-info">
        Welcome to our system!
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
    } else {
        // Token không hợp lệ hoặc đã hết hạn
        ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Account Activation Failed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/active.css">
</head>

<body>
  <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
    <div class="form-box">
      <div class="error-icon"></div>
      <h2>Account Activation Failed!</h2>
      <p class="error-message">
        The activation link is invalid or has expired.
      </p>
      <div class="d-grid">
        <a href="/php/?module=auth&action=login" class="btn btn-primary">Back to Login</a>
      </div>
      <p class="additional-info">
        Please contact support if you continue to have issues.
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
    }
} else {
    // Không có token trong URL
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Invalid Link</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/active.css">
</head>

<body>
  <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
    <div class="form-box">
      <div class="error-icon"></div>
      <h2>Invalid Activation Link!</h2>
      <p class="error-message">
        The activation link is missing or invalid.
      </p>
      <div class="d-grid">
        <a href="/php/?module=auth&action=login" class="btn btn-primary">Back to System</a>
      </div>
      <p class="additional-info">
        Please check your email for the correct activation link.
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
}

?>