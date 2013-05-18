<?php

/**
 * @file
 * A single location to store configuration.
 */
define('CONSUMER_KEY', 'KFN0e4SVjeB0RbOH8ZQ');
define('CONSUMER_SECRET', 'QMuuCK1DNRbYLzyUsFZxgUMxUR3QsGyzxkAxLxhIdo');
define('OAUTH_CALLBACK', 'https://j.laot.tk/twitter_oauth.php');
define('BASE_URL', 'https://j.laot.tk/');

define('DATA_DIRECTORY', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR);

define('DEBUG', true);

define('DOUBAN_ID', 'jessetoo');
define('DOUBAN_NICKNAME', '灵菲茜语');
define('TWITTER_ID', 'jessetoo');

header('Content-Type: text/html; charset=utf-8'); 
session_start();