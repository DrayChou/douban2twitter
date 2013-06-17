<?php


include 'include/config.php';
include 'include/common.php';
include 'include/twitteroauth/twitteroauth/twitteroauth.php';

error_reporting(E_ALL);

define('APPROOT', dirname(__FILE__).'/');
define('USER_DIR', APPROOT.'data'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR);
define('LOG_DIR', APPROOT.'data'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR);

session_start();