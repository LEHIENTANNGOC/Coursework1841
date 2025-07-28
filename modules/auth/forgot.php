<?php

if (!defined('_TanHien')) {
    die('Access denied');
}


if (isPost()) { 
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


    if(empty($error)){
      //xu ly email
      if(!empty($filter['email'])){
      $email = $filter['email'];
      
      $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");

      if(!empty($checkEmail)){
        //update forgot_token vao bang users
        $forgot_token = sha1(uniqid().time());
        $data = [
          'forgot_token' => $forgot_token
        ];
        $condition = 'user_id='.$checkEmail['user_id'];
        $updateStatus = update('users',$data,$condition);
        if($updateStatus){
                    // send email
          $emailTo = $email;
          $subject = 'Reset mat khau';
          $content = 'Hello,<br>';
          $content .= 'You have requested to reset your password.<br>';
          $content .= 'To reset your password, please click the link below:<br>';
          $content .= _HOST_URL . '/?module=auth&action=reset&token=' . $forgot_token . '<br>';
          $content .= 'Thank you for supporting Tan Hien';
          
          sendEmail($emailTo, $subject, $content);
          
          setFlashSession('msg', 'Password reset link sent to your email.');
          setFlashSession('msg_type', 'success');
        }else{
          setFlashSession('msg', 'Failed to process request. Please try again.');
          setFlashSession('msg_type', 'danger');
        }
      }else{
        setFlashSession('msg', 'Email not found in our records.');
        setFlashSession('msg_type', 'danger');
      }
      }else{
      setFlashSession('msg', 'Email is required.');
      setFlashSession('msg_type', 'danger');
      }
    }else{
      setFlashSession('msg', 'Please fix the errors below.');
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot</title>
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/forgot.css">
</head>

<body>
  <div class="container">
    <div class="form-box" id="forgot-password-form">
      <h2>Forgot Password</h2>
      <p class="description">
        Enter your email address and we'll send you a link to reset your password.
      </p>

      <form id="forgotPasswordForm" action="" method="post" enctype="multipart/form-data">
        <input type="email" placeholder="Enter your email address" name="email" id="email"
          value="<?php echo !empty($oldData['email']) ?($oldData['email']) : ''; ?>" required> <button type="submit"
          id="submit-btn">Send Reset Link</button>
      </form>

    </div>
  </div>

</body>

</html>