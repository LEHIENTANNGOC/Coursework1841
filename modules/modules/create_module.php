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

      // validate module name
      if (empty(trim($filter['module_name']))) {
          $error['module_name']['required'] = 'Module name is required';
      } else {
          if (strlen(trim($filter['module_name'])) < 2 || strlen(trim($filter['module_name'])) > 100) {
              $error['module_name']['length'] = 'Module name must be between 2 and 100 characters';
          } else {
              // Check if module name already exists
              $moduleName = trim($filter['module_name']);
              $checkModule = getRows("SELECT module_id FROM modules WHERE module_name = '$moduleName'");
              if ($checkModule > 0) {
                  $error['module_name']['exists'] = 'Module name already exists';
              }
          }
      }
      
      // check error
      if (empty($error)) {
          $data = [
              'module_name' => trim($filter['module_name']),
              'user_id' => getSession('user_id')
          ];

          $insertStatus = insert('modules', $data);
          
          if ($insertStatus) {
              redirect('?module=dashboard&action=dashboard_module');
              
          } else {         
              $msg = 'Failed to create module, please try again later.';
              $msg_type = 'danger';
          }

      } else {
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
  <title>Create New Module - QUIZZ</title>
  <link rel="stylesheet" href="/php/public/assets/css/auth.css">
  <link rel="stylesheet" href="/php/public/assets/css/create_module.css">
</head>

<body>
  <div class="container">

    <div class="form-box">
      <h2>Create New Module</h2>

      <?php 
        if(!empty($msg) && !empty($msg_type)){
            getMsg($msg, $msg_type);
        }
        ?>

      <form action="" method="POST">

        <div class="form-group">
          <input type="text" class="form-control" name="module_name"
            value="<?php echo !empty($filter['module_name']) ? htmlspecialchars($filter['module_name']) : ''; ?>"
            placeholder="Enter module name...">
          <div class="error">
            <?php echo !empty($errorArr['module_name']) ? reset($errorArr['module_name']) : ''; ?>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Module</button>

      </form>

      <p class="mt-3">
        <a href="?module=home&action=home_auth">Back to Home</a>
      </p>

    </div>

  </div>
</body>

</html>