<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/sessions.php';
require_once __DIR__ . '/../../includes/database.php';

if (!defined('_TanHien')) {
    die('Access denied');
}



// validate input
// check data with database
// du lieu khop -> tao token_login - insert vao token_login(kiem tra dang nhap)
// dieu huong den dashboard
if (isPost()){
  $filter = filterData();
  $error = [];

  
  // validate email
  if (empty(trim($filter['email']))) {
      $error['email']['required'] = 'Email is required';
  } else {
      if (!validateEmail(trim($filter['email']))) {
          $error['email']['isEmail'] = 'Email is not valid';
      }
  }

  
  // validate password
  if (empty(trim($filter['password']))) {
      $error['password']['required'] = 'Password is required';
  } else {
      if (strlen(trim($filter['password'])) < 6 || strlen(trim($filter['password'])) > 20) {
          $error['password']['length'] = 'Password must be between 6 and 20 characters';
      }
  }

  if(empty($error)){

    //Kiem tra du lieu
    $email = $filter['email'];
    $password = $filter['password'];

    $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'"); 
    
    if(!empty($checkEmail)){
      // Kiểm tra trạng thái tài khoản
      if($checkEmail['status'] == 0){
        setFlashSession('msg', 'Tài khoản chưa được kích hoạt');
        setFlashSession('msg_type', 'warning');
        redirect('?module=auth&action=login');
      }
      
      if(!empty($password)){
        $checkStatus = password_verify($password, $checkEmail['password']);
        if($checkStatus){
          //tk chi login 1 noi
          $user_id = $checkEmail['user_id'];
          $checkAlready = getRows("SELECT * FROM login_tokens WHERE user_id = '$user_id'");
          
          if($checkAlready >= 1){
            setFlashSession('msg', 'Tài khoản đã được đăng nhập ở nơi khác');
            setFlashSession('msg_type', 'danger');  
            redirect('?module=auth&action=login');
          }else{   
          //tao token va insert vao bang login token
          $token = sha1(uniqid().time());


          // gan token len seesion
          setSession('login_tokens', $token);
          setSession('user_id', $checkEmail['user_id']); 
          setSession('permission', $checkEmail['permission']);
          setSession('email', $checkEmail['email']);
          setSession('username', $checkEmail['username']);
            
          //tao token va insert vao bang login token
          $data = [
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $checkEmail['user_id'],
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
          ];
          $insertToken = insert('login_tokens', $data);
          if($insertToken){
            redirect('/?module=home&action=home_auth');

          }else{
            setFlashSession('msg', 'Login failed!, TRY AGAIN');
            setFlashSession('msg_type', 'danger');
          }
          }

        }else{
          setFlashSession('msg', 'Login failed!, TRY AGAIN');
          setFlashSession('msg_type', 'danger');
        }
      }
    } 
    
    
    else {
      setFlashSession('msg', 'Login failed!, TRY AGAIN');
      setFlashSession('msg_type', 'danger');
    }
    
  } else {
    setFlashSession('msg', 'Login failed!, TRY AGAIN');
    setFlashSession('msg_type', 'danger');

    setFlashSession('oldData', $filter);
    setFlashSession('error', $error);
  }

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
  <title>LOGIN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
</head>

<body>
  <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
    <div class="form-box">
      <?php 
        if(!empty($msg) && !empty($msg_type)){
          getMsg($msg, $msg_type);
      }
      ?>
      <form action="" method="post" enctype="multipart/form-data">
        <h2>Login</h2>
        <input type="email" class="form-control" placeholder="Email" name="email"
          value="<?php echo !empty($oldData['email']) ?($oldData['email']) : ''; ?>">
        <div class="error">
          <?php echo !empty($errorArr['email']) ? reset($errorArr['email']) : false; ?>
        </div>
        <input type="password" class="form-control" placeholder="Password" name="password">
        <div class="error">
          <?php echo !empty($errorArr['password']) ? reset($errorArr['password']) : false; ?>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
        <p class="mt-3">Don't have an account? <a href="/php/?module=auth&action=register">Register</a></p>
        <p class="mt-2 text-center">
          <a href="/php/?module=auth&action=forgot">Forgot your password?</a>
        </p>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>