<?php


if (!defined('_TanHien')) {
    die('Access denied');
}

$msg = '';
$msg_type = '';
$errorArr = [];

if (isPost()) { 
    $filter = filterData();
    $error = [];

    // validate username
    if (empty($filter['username'])) {
        $error['username']['required'] = 'Username is required';
    } else {
        if (strlen($filter['username']) < 3 || strlen($filter['username']) > 20) {
            $error['username']['length'] = 'Username must be between 3 and 20 characters';
        }
    }

    // validate email
    if (empty(trim($filter['email']))) {
        $error['email']['required'] = 'Email is required';
    } else {
        if (!validateEmail(trim($filter['email']))) {
            $error['email']['isEmail'] = 'Email is not valid';
        } else {
            $email = $filter['email'];
            $checkEmail = getRows("SELECT user_id FROM users WHERE email = '$email'");
            if ($checkEmail > 0) {
                $error['email']['exists'] = 'Email already exists';
            }
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

    // validate confirm password
    if (empty(trim($filter['confirm_password']))) {
        $error['confirm_password']['required'] = 'Confirm password is required';
    } else {
        if (trim($filter['password']) !== trim($filter['confirm_password'])) {
            $error['confirm_password']['match'] = 'Confirm password does not match';
        }
    }

    // validate permission
    if (empty($filter['permission'])) {
        $error['permission']['required'] = 'Permission is required';
    }
    
    // check error
    if (empty($error)) {
        $activeToken = sha1(uniqid() . time());

        $data = [
            'username' => $filter['username'],
            'email' => $filter['email'],
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
            'permission' => $filter['permission'],
            'active_token' => $activeToken,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insertStatus = insert('users', $data);
        
        if ($insertStatus) {
            // send email
            $emailTo = $filter['email'];
            $subject = 'Account Activation Successful!';
            $content = 'Congratulations! You have successfully registered your account.<br>';
            $content .= 'To activate your account, please click the link below:<br>';
            $content .= _HOST_URL . '/?module=auth&action=active&token=' . $activeToken . '<br>';
            $content .= 'Thank you for supporting Tan Hien';
            
            sendEmail($emailTo, $subject, $content);
            
            setFlashSession('msg', 'Registration successful, please activate your account.');
            setFlashSession('msg_type', 'success');

        } else {         
            setFlashSession('msg', 'Registration failed, please try again later.');
            setFlashSession('msg_type', 'danger');
        }

    } else {
        setFlashSession('msg', 'Registration failed!, TRY AGAIN');
        setFlashSession('$msg_type', 'danger');

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
  <title>REGISTER</title>
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
        <h2>Register</h2>
        <input type="text" class="form-control"
          value="<?php echo !empty($oldData['username']) ?($oldData['username']) : ''; ?>" placeholder="Username"
          name="username">
        <div class="error">
          <?php echo !empty($errorArr['username']) ? reset($errorArr['username']) : false; ?>
        </div>
        <input type="email" class="form-control"
          value="<?php echo !empty($oldData['email']) ?($oldData['email']) : ''; ?>" placeholder="Email" name="email">
        <div class="error">
          <?php echo !empty($errorArr['email']) ? reset($errorArr['email']) : false; ?>
        </div>

        <input type="password" class="form-control" placeholder="Password" name="password">
        <div class="error">
          <?php echo !empty($errorArr['password']) ? reset($errorArr['password']) : false; ?>
        </div>
        <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password">
        <div class="error">
          <?php echo !empty($errorArr['confirm_password']) ? reset($errorArr['confirm_password']) : false; ?>
        </div>
        <select name="permission" class="form-select">
          <option value="">Select Role</option>
          <option value="user"
            <?php echo (!empty($oldData['permission']) && $oldData['permission'] == 'user') ? 'selected' : ''; ?>>User
          </option>
          <option value="admin"
            <?php echo (!empty($oldData['permission']) && $oldData['permission'] == 'admin') ? 'selected' : ''; ?>>Admin
          </option>
        </select>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Register</button>
        </div>
        <p class="mt-3">Already have an account? <a href="/php/?module=auth&action=login">Login</a></p>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>