<?php

if (!defined('_TanHien')) {
    die('Access denied');
}


// Set sesion 

function setSession($key, $value) {
    if(!empty(session_id())){
        $_SESSION[$key] = $value;
        return true;
    }

    return false;
}

function getSession($key = ''){
    if(empty($key)){
        return $_SESSION;
    }else {
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
    }
    return false;
}


// Delete session
function removeSession($key='') {
   if(empty($key)){
        session_destroy();
        return true;
    }else{
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
        return true;
    }
    return false;
}

// Set flash session

function setFlashSession($key, $value) {
    $key = $key .'Flash';
    $rel = setSession($key, $value);
    return $rel;
}


// Get flash session
function getFlashSession($key='') {
    $key = $key .'Flash';
    $rel = getSession($key);

    removeSession($key);

    return $rel;
}