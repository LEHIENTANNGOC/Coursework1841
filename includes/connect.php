<?php

if (!defined('_TanHien')) {
    die('Access denied');
}


try {
    if(class_exists('PDO')){
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", //Support UTF-8
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Đẩy lỗi vào ngoại lệ
        );
        $dsn = _DRIVER.':host='._HOST.';dbname='._DB;
        $conn = new PDO($dsn, _USER, _PASS, $options);
        
    }
}catch (Exception $ex){
    require_once __DIR__ . '/../modules/errors/404.php';
    die();
}