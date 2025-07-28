<?php

const _TanHien = true;

const _MODULES = 'home'; 
const _ACTION = 'index';

//declare database

const _HOST = 'localhost';
const _DB = 'cw';
const _USER = 'root';
const _PASS = '';
const _DRIVER = 'mysql';

// debug error
const _DEBUG = true;


// Host URL constants
define('_HOST_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/php');
define('_HOST_URL_TEMPLATE', _HOST_URL . '/templates');

// Path constants
define('_PATH_URL', __DIR__);
define('_PATH_URL_TEMPLATE', _PATH_URL . '/templates');