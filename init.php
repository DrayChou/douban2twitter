<?php

require 'include/config.php';
require 'include/common.php';
require 'include/twitteroauth/twitteroauth.php';

define('APPROOT', dirname(__FILE__));
define('USER_DIR', APPROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR);
define('LOG_DIR', APPROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);

session_start();