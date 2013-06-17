<?php
include dirname(dirname(__FILE__)).'/init.php';

if ( !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) ) {
    //登陆完毕之后干嘛
    //echo "已取得授权。。。";
    //看推
    $username = isset($_GET['t']) ? $_GET['t'] : $_SESSION['screen_name'];
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
	$twitteroauth->host = "https://api.twitter.com/1.1/";
	#$twitteroauth->ssl_verifypeer = TRUE;
    
    $result = $twitteroauth->get('users/lookup', array('screen_name' => $username));
    if(DEBUG){
        set_douban_debug_log($_SESSION);
    }

    if(!isset($result[0])){
	    session_destroy();
	    echo "调用 twitter API 查询用户信息失败，请刷新页面重新验证，或者通知管理员<br/>";
        echo '<a href="javascript:window.top.location.reload();" >返回</a>';
        die();
    }
    $twitter = @$result[0];
    $userinfo = get_twitter_config($_SESSION['screen_name']);
    $_SESSION['douban'] = $userinfo['douban'];

	$setp = isset($_GET["setp"]) ? $_GET["setp"] : '-1';
	if ( $setp == "0" ) {
	    session_destroy();
        echo "<script>window.top.location.reload();</script>";
        die();
	} elseif ( $setp == "1" ){
		$douban_screen_name = isset($_POST["dn"]) ? $_POST["dn"] : '';
		$douban_user_info = get_douban_userinfo( $douban_screen_name );

        if(DEBUG){
            set_douban_debug_log($douban_user_info);
        }
		
		if( empty($douban_user_info) ){
			echo "豆瓣ID错误，或者豆瓣 API 查询失败，请返回重新填写 <br/>";
            echo '<a href="javascript:window.history.go(-1);" >返回</a>';
            die();
		}
		$_SESSION['douban'] = $douban_user_info;

        if(DEBUG){
            set_douban_debug_log($_SESSION);
        }

		set_twitter_config($_SESSION);
    	//header('Location: /index.php');
	} elseif ( $setp == "2" ){
        $_SESSION['douban'] = null;
        set_twitter_config($_SESSION);
        //header('Location: /index.php');
    }else{
	    set_twitter_config($_SESSION);
    }
    
} elseif ( !empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']) ) {
    // 数据合法，继续
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $twitteroauth->host = "https://api.twitter.com/1.1/";
	#$twitteroauth->ssl_verifypeer = TRUE;

    // 获取 access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
    
    // 将获取到的 access token 保存到 Session 中
    $_SESSION['access_token'] = $access_token;
    $_SESSION['user_id'] = $access_token["user_id"];
    $_SESSION['screen_name'] = $access_token["screen_name"];
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);

    //set_twitter_config($_SESSION);
    header('Location: /index.php');
} else {
    // 数据不完整，转到上一步
    unset($_SESSION['access_token']);

    // 创建 TwitterOAuth 对象实例
	$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$twitteroauth->host = "https://api.twitter.com/1.1/";
	#$twitteroauth->ssl_verifypeer = TRUE;

    //var_dump($twitteroauth);die();

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
        <title>豆瓣说说[电台]同步到 Twitter.</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
            img {border-width: 0}
            * {font-family:'Lucida Grande', sans-serif;}
        </style>
    </head>
    <body style="background-image: <?= $twitter->profile_background_image_url ?>;">
        <div>
            <h2>豆瓣说说[电台]同步到 Twitter.</h2>

            <div>
                <?php if (!empty($twitter)): ?>
                    <h3>Twitter</h3>
                    <img src="<?= $twitter->profile_image_url_https ?>" title="<?= $twitter->name ?>"/><br/>
                    name:<?= $twitter->name ?><br/>
                    bio:<?= $twitter->description ?><br/>
                    <p><a href='./index.php?setp=0'>退出登录</a></p>

					 <div>
		                <?php if (!empty($_SESSION['douban'])): ?>
                            <h3>DouBan</h3>
                            <p>同步...(每5分钟抓取同步一次</p>
		                    <img src="<?= $_SESSION['douban']['avatar'] ?>" title="<?= $_SESSION['douban']['name'] ?>"/><br/>
		                    name:<?= $_SESSION['douban']['name'] ?><br/>
		                    bio:<?= $_SESSION['douban']['desc'] ?><br/>
                            <p><a href='./index.php?setp=2'>取消同步</a></p>
		                <?php else:?>
                            <h4>要转发的豆瓣ID</h4>
		                    <form method="post" action="index.php?setp=1">
		                    	<input type="text" name="dn">
		                    	<input type="submit">
		                    </form>
		                <?php endif; ?>
		            </div>
                    
                <?php else:?>
                    <a href='<?=$oauth_url?>'>twitter</a>
                <?php endif; ?>
            </div>
    </body>
</html>

