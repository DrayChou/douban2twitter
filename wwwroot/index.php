<?php
include dirname(dirname(__FILE__)).'init.php';

die('122');


if ($_GET["setp"] == "0") {
    session_start();
    session_destroy();
}

if (!empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret'])) {
    //登陆完毕之后干嘛
    echo "已取得授权。。。";
    //看推
    $username = isset($_GET['t']) ? $_GET['t'] : "Timy_127";
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $result = $twitteroauth->get('users/lookup', array('screen_name' => $username));
    //echo '<pre>',var_dump($result,true),'</pre>';
    $twitter = $result[0];
} elseif (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
    // 数据合法，继续
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    // 获取 access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
    // 将获取到的 access token 保存到 Session 中
    $_SESSION['access_token'] = $access_token;
    $_SESSION['user_id'] = $access_token["user_id"];
    $_SESSION['screen_name'] = $access_token["screen_name"];

    set_twitter_config($access_token);
    header('Location: /index.php');
} elseif ($_GET["setp"] == "1"){
	
} else {
    // 数据不完整，转到上一步
    unset($_SESSION['access_token']);
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
	    $oauth_url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
	} else {
	    // 发生错误，你可以做一些更友好的处理
	    die('Something wrong happened.');
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Twitter OAuth in PHP</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
            img {border-width: 0}
            * {font-family:'Lucida Grande', sans-serif;}
        </style>
    </head>
    <body style="background-image: <?= $twitter->profile_background_image_url ?>;">
        <div>
            <h2>Welcome to a Twitter OAuth PHP example.</h2>

            <div>
                <?php if (!empty($twitter)): ?>
                    <img src="<?= $twitter->profile_image_url_https ?>" title="<?= $twitter->name ?>"/><br/>
                    name:<?= $twitter->name ?><br/>
                    bio:<?= $twitter->description ?><br/>
                    <p><a href='./index.php?setp=0'>clearing your session</a></p>
                <?php else:?>
                    <a href='<?=$oauth_url?>'>twitter</a>
                <?php endif; ?>
            </div>
    </body>
</html>
