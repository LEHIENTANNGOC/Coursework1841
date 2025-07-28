<?php
if (!defined('_TanHien')) {
    die('Access denied');
}

if (isLogin()){
  $token = getSession('login_tokens');
  $removeToken = delete('login_tokens',"token = '$token'");

  if($removeToken){
    removeSession('login_tokens'); 
    redirect('/?module=home'); 
  }else{
    setFlashSession('msg', 'Logout failed!, TRY AGAIN');
    setFlashSession('msg_type', 'danger');
  }
  

}else{
  redirect('/?module=home');
}
?>