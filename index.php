<?php
if ($_GET["setp"] == "0") {
    session_start();
    session_destroy();
}

require 'config.php';
require 'common.php';
require 'twitteroauth/twitteroauth.php';

$twitter_config = get_twitter_config(TWITTER_ID);
if (!empty($twitter_config['oauth_token']) && !empty($twitter_config['oauth_token_secret'])) {
    //看推
    $username = isset($_GET['t']) ? $_GET['t'] : "YT_mei";
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitter_config['oauth_token'], $twitter_config['oauth_token_secret']);
    $result = $twitteroauth->get('users/lookup', array('screen_name' => $username));
    $twitter = $result[0];
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

            <p><a href='./twitter_login.php'>twitter</a>.<a href='./index.php?setp=0'>clearing your session</a></p>
            <div>
                <?php if (!empty($twitter)): ?>
                    <img src="<?= $twitter->profile_image_url_https ?>" title="<?= $twitter->name ?>"/><br/>
                    name:<?= $twitter->name ?><br/>
                    bio:<?= $twitter->description ?><br/>
                <?php endif; ?>
            </div>
    </body>
</html>
