<?php

require 'config.php';
require 'twitteroauth/twitteroauth.php';

// 创建 TwitterOAuth 对象实例
$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
// Requesting authentication tokens, the parameter is the URL we will be redirected to
$request_token = $twitteroauth->getRequestToken(OAUTH_CALLBACK);

// 保存到 session 中
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

// 如果没有错误发生
if ($twitteroauth->http_code == 200) {
    // Let's generate the URL and redirect
    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
    header('Location: ' . $url);
} else {
    // 发生错误，你可以做一些更友好的处理
    die('Something wrong happened.');
}