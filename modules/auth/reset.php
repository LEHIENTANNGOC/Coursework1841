<?php
// kiem tra forget_token neu nguoi dung co gui yeu cau 

if (!defined('_TanHien')) {
    die('Access denied');
}

$filterGet = filterData('get');

if(!empty($filterGet['token'])){
  $tokenReset = $filterGet['token'];
}

if(!empty($tokenReset)){
  //check token co chinh xac ko
  $checkToken = getOne("SELECT * FROM users WHERE forgot_token = '$tokenReset'");
  if($checkToken){
        // ko gap loi
      if (isPost()){
          $filter = filterData();
          $error = [];

          // validate new password
          if (empty(trim($filter['new_password']))) {
              $error['new_password']['required'] = 'Password is required';
          } else {
              if (strlen(trim($filter['new_password'])) < 8 || strlen(trim($filter['new_password'])) > 20) {
                  $error['new_password']['length'] = 'Password must be between 8 and 20 characters';
              }
          }

          

          //validate confirm password
          if (empty(trim($filter['confirm_new_password']))) {
              $error['confirm_new_password']['required'] = 'Confirm password is required';
          } else {
              if (trim($filter['new_password']) !== trim($filter['confirm_new_password'])) {
                  $error['confirm_new_password']['match'] = 'Confirm password does not match';
              }
          }

          // Nếu không có lỗi, cập nhật password
          if (empty($error)) {
            $password = password_hash($filter['new_password'], PASSWORD_DEFAULT);
            $data = [
              'password' => $password,
              'forgot_token' => null,
            ];
            $condition = "user_id = " . $checkToken['user_id'];
            $updateStatus = update('users', $data, $condition);
            
            if($updateStatus){
              // send email
              $emailTo = $checkToken['email'];
              $subject = 'doi mat khau thanh cong';
              $content = 'chuc mung doi mat khau thanh cong<br>';
              $content .= 'neu khong phai ban lien het voi admin !!.<br>';
              $content .= 'Thank you for supporting Tan Hien';
              
              sendEmail($emailTo, $subject, $content);
              
              // Redirect to login page after successful password reset
              redirect('/?module=auth&action=login');
            }else{
                setFlashSession('msg', 'doi mat khau that bai');
                setFlashSession('msg_type', 'danger');
            }
            
          }else{
          setFlashSession('msg', 'Please fix the errors below');
          setFlashSession('msg_type', 'danger');

          setFlashSession('oldData', $filter);
          setFlashSession('error', $error);
          }
      }
    
  }else{
    setFlashSession('msg', 'liet ket da het han hoac khong ton tai');
    setFlashSession('msg_type', 'danger');
  }

  
}else{
    setFlashSession('msg', 'liet ket da het han hoac khong ton tai');
    setFlashSession('msg_type', 'danger');
}



$msg = getFlashSession('msg');
$msg_type = getFlashSession('msg_type');
$oldData = getFlashSession('oldData');
$errorArr = getFlashSession('error');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RESET PASSWORD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/reset.css">

</head>

<body>
  <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">

    <div class="form-box">
      <?php 
      if(!empty($msg) && !empty($msg_type)){
          getMsg($msg, $msg_type);
      } 
      ?>
      <form action="" method="POST" enctype="multipart/form-data">
        <h2>Reset Password</h2>
        <p class=" info-text">Enter your new password</p>

        <input type="password" class="form-control" placeholder="New Password" name="new_password">
        <div class="error">
          <?php echo !empty($errorArr['new_password']) ? reset($errorArr['new_password']) : ''; ?>
        </div>

        <input type="password" class="form-control" placeholder="Confirm New Password" name="confirm_new_password">
        <div class="error">
          <?php echo !empty($errorArr['confirm_new_password']) ? reset($errorArr['confirm_new_password']) : ''; ?>
        </div>

        <div class="password-requirements">
          <p><strong>Password Requirements:</strong></p>
          <ul>
            <li>At least 8 characters</li>
            <li>Contains uppercase and lowercase letters</li>
            <li>Contains at least 1 number</li>
            <li>Contains at least 1 special character</li>
          </ul>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Change Password</button>
        </div>

        <p class="mt-3">Remember your password? <a href="/php/?module=auth&action=login">Login</a></p>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>