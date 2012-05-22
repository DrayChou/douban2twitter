<?php

require 'config.php';
require 'common.php';
require 'twitteroauth/twitteroauth.php';

$last_douban = get_douban_log(DOUBAN_ID);
$new_douban = get_douban(DOUBAN_ID);
$twitter_config = get_twitter_config(TWITTER_ID);

if (empty($twitter_config)) {
    unset($_SESSION['access_token']);
    header('Location: twitter_login.php');
}

if (empty($last_douban)) {
    $last_douban["time"] = 0;
}

echo "进入发布页<br/>\n";
$newst_douban = array();
foreach ($new_douban as $value) {
    if ($value["time"] > $last_douban["time"]) {
        echo "<br/>发布:" . $value["content"] . "<br/>\n";
        $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitter_config['oauth_token'], $twitter_config['oauth_token_secret']);
        $result = $twitteroauth->post('statuses/update', array('status' => $value["content"]));
        if (!empty($result->id_str)) {
            $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
            echo "成功：<a target='_blank' href='{$href}'>地址</a><br/>\n\n";
        } else {
            set_douban_error_log(DOUBAN_ID, array("douban" => $value, "result" => $result));
        }

        if (empty($newst_douban) || $value["time"] > $newst_douban["time"]) {
            $newst_douban = $value;
        }
    }
}
if (empty($newst_douban)) {
    echo "没有新状态需要发送<br/>\n";
} else {
    set_douban_log(DOUBAN_ID, $newst_douban);
    echo "发布完成<br/>\n";
}