<?php

require 'include/config.php';
require 'include/common.php';
require 'include/twitteroauth/twitteroauth.php';

default('APPROOT',dirname(__FILE__));

session_start();