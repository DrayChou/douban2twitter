<?php

require 'config.php';
require 'common.php';
require 'twitteroauth/twitteroauth.php';

if (!empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret'])) {
    //登陆完毕之后干嘛
    echo "已取得授权。。。";
    echo <<<END
<script language="javascript" type="text/javascript">
    var hwd;
    var intSec = 3;//这里定义时间：秒
    function reHandle()
    {
        if(intSec==0) {
            window.location.href = 'index.php';
        } else {
            intSec--;
        }
        hwd = setTimeout(reHandle,1000);
    }
    reHandle();
    document.write('<p><span id="tiao">'+intSec+'</span>秒后自动跳转…</p>')
</script>
END;
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
    echo "取得授权。。。";
    echo <<<END
<script language="javascript" type="text/javascript">
    var hwd;
    var intSec = 3;//这里定义时间：秒
    function reHandle()
    {
        if(intSec==0) {
            window.location.href = 'index.php';
        } else {
            intSec--;
        }
        hwd = setTimeout(reHandle,1000);
    }
    reHandle();
    document.write('<p><span id="tiao">'+intSec+'</span>秒后自动跳转…</p>')
</script>
END;
} else {
    // 数据不完整，转到上一步
    unset($_SESSION['access_token']);
    header('Location: twitter_login.php');
}