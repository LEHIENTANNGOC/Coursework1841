<?php

if (!defined('_TanHien')) {
    die('Access denied');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// function send email
function sendEmail($emailTo,$subject,$content){
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'lengoctanhien090@gmail.com';                     //SMTP username
    $mail->Password   = 'itib vuwm kudy lpir';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('lengoctanhien090@gmail.com', 'TanHien');
    $mail->addAddress($emailTo);     //Add a recipient

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->CharSet = 'UTF-8'; //Set character encoding to UTF-8
    $mail->Subject = $subject;
    $mail->Body    = $content;

    return $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}

// check method post
function isPost(){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        return true;
    }
    return false;
}

// check method get
function isGet(){
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        return true;
    }
    return false;
}

//filter data
function filterData($method = '')  {
    $filterArr = [];
    if(empty($method)) {
        if(isGet()){
            if(!empty($_GET)){
                foreach($_GET as $key => $value) {
                   $key = strip_tags($key);
                   if(is_array($value)){
                       $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                   }
                   else {
                       $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                   }
                }
            } 
        }
        if(isPost()){
            if(!empty($_POST)){
                foreach($_POST as $key => $value) {
                   $key = strip_tags($key);
                   if(is_array($value)){
                       $filterArr[$key] = filter_input(INPUT_POST,$key, FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                   }
                   else {
                       $filterArr[$key] = filter_input(INPUT_POST,$key, FILTER_SANITIZE_SPECIAL_CHARS);
                   }
                }
            } 
        }
    } else {
        if($method == 'get'){
            if(!empty($_GET)){
                foreach($_GET as $key => $value) {
                   $key = strip_tags($key);
                   if(is_array($value)){
                       $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                   }
                   else {
                       $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                   }
                }
            } 
        } elseif($method =='post'){
            if(!empty($_POST)){
                foreach($_POST as $key => $value) {
                   $key = strip_tags($key);
                   if(is_array($value)){
                       $filterArr[$key] = filter_input(INPUT_POST,$key, FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                   }
                   else {
                       $filterArr[$key] = filter_input(INPUT_POST,$key, FILTER_SANITIZE_SPECIAL_CHARS);
                   }
                }
            } 
            
        }
    }

    return $filterArr;
}

//validate email
function validateEmail($email) {
    if(!empty($email)){
        $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        } 
        return $checkEmail;
        
}

//validate int
function validateInt($number) {
    if(!empty($number)){
        $checkNumber = filter_var($number, FILTER_VALIDATE_INT);
    } 
    return $checkNumber;
}

//validate phone 0 123456789
function isPhone($phone) {
    $phoneFirst = false;
    $checkPhone = false;
    if (!empty($phone)) {
        if ($phone[0] == '0') {
            $phoneFirst = true;
            $phone = substr($phone, 1);
        }
        if (validateInt($phone) && strlen($phone) == 9) {
            $checkPhone = true;
        }
    }
    if ($phoneFirst && $checkPhone) {
        return true;
    }
    return false;
}

// noficationErr

function getMsg($msg, $type = 'success') {
    echo '<div class="announce-message alert alert-' . $type . '">';
    echo $msg;
    echo '</div>';
}

// redirect('http://localhost/php/?module=auth&action=login',true);
// redirect('?module=auth&action=login);
// ham chuyen huong
// Hàm chuyển hướng trang
function redirect($path, $pathFull = false) {
    if ($pathFull) {
        header("Location: $path");
        exit();
    } else {
        $url = _HOST_URL . $path;
        header("Location: $url");
        exit(); 

    }
}

function isLogin(){
    $checkLogin = false;
    $tokenLogin = getSession('login_tokens'); 
    $checkToken = getOne("SELECT * FROM login_tokens WHERE token = '$tokenLogin'");
    if(!empty($checkToken)){
        $checkLogin = true;
    } else{
        removeSession('login_tokens'); 
    }
    return $checkLogin;
}