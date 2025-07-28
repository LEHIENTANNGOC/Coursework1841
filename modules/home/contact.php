<?php



if(!isLogin()){
    redirect('?module=auth&action=login');
}

if (!defined('_TanHien')) {
    die('Access denied');
}

require_once 'posts_helper.php';

$currentUser = getCurrentUser();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

$msg = '';
$msg_type = '';
$errorArr = [];

if (isPost()) {
    $filter = filterData();
    $error = [];

    // validate username
    if (empty($filter['username'])) {
        $error['username']['required'] = 'Name is required';
    } else {
        if (strlen($filter['username']) < 2 || strlen($filter['username']) > 50) {
            $error['username']['length'] = 'Name must be between 2 and 50 characters';
        }
    }

    // validate message
    if (empty(trim($filter['message']))) {
        $error['message']['required'] = 'Message is required';
    } else {
        if (strlen(trim($filter['message'])) < 10 || strlen(trim($filter['message'])) > 1000) {
            $error['message']['length'] = 'Message must be between 10 and 1000 characters';
        }
    }

    // check error
    if (empty($error)) {
        $data = [
            'name' => $filter['username'],
            'email' => $currentUser['email'], // Sử dụng email từ user đang đăng nhập
            'message' => $filter['message'],
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        $insertStatus = insert('contact_messages', $data);

        if ($insertStatus) {
            // send email notification
            $emailTo = 'admin@tanhien.com'; // Admin email
            $subject = 'New Contact Message from ' . $filter['username'];
            $content = 'You have received a new contact message:<br><br>';
            $content .= '<strong>Name:</strong> ' . $filter['username'] . '<br>';
            $content .= '<strong>Email:</strong> ' . $currentUser['email'] . '<br>';
            $content .= '<strong>Message:</strong><br>' . nl2br($filter['message']) . '<br><br>';
            $content .= '<strong>Sent at:</strong> ' . date('Y-m-d H:i:s');

            sendEmail($emailTo, $subject, $content);

            redirect('?module=home&action=home_auth');

            

        } else {
            setFlashSession('msg', 'Failed to send message, please try again later.');
            setFlashSession('msg_type', 'danger');
        }

    } else {
        setFlashSession('msg', 'Please fix the errors below and try again.');
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
  <title>CONTACT</title>
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
      <form action="" method="post">
        <h2>Contact Us</h2>
        <p class="form-description">
          We'd love to hear from you! Send us a message and we'll respond as soon as possible.
        </p>

        <input type="text" class="form-control"
          value="<?php echo !empty($oldData['username']) ? htmlspecialchars($oldData['username']) : htmlspecialchars($currentUser['username']); ?>"
          placeholder="Your Name" name="username">
        <div class="error">
          <?php echo !empty($errorArr['username']) ? reset($errorArr['username']) : ''; ?>
        </div>


        <textarea class="form-control" placeholder="Your Message"
          name="message"><?php echo !empty($oldData['message']) ? htmlspecialchars($oldData['message']) : ''; ?></textarea>
        <div class="error">
          <?php echo !empty($errorArr['message']) ? reset($errorArr['message']) : ''; ?>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Send Message</button>
        </div>

        <p class="mt-3 text-center">Need immediate help? <a href="/php/?module=support">Support Center</a></p>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>