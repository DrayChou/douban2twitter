<?php

require dirname(dirname(__FILE__)).'/init.php';

foreach (glob(USER_DIR."*.twitter.config") as $filename) {
    echo "$filename size " . filesize($filename) . "\n";
    $jsonStr = file_get_contents($filename);
    $userinfo = unserialize($jsonStr);
    //var_dump($userinfo);

    #douban_id
    if( !isset($userinfo['douban']['name']) ){
        continue;
    }
    $douban_id = $userinfo['douban']['name'];
    echo 'douban_id:',$douban_id,"<br/>\n\n";

    #oauth_token
    if( !isset($userinfo['access_token']['oauth_token']) ){
        continue;
    }
    $twitter_oauth_token = $userinfo['access_token']['oauth_token'];
    echo 'twitter_oauth_token:',$twitter_oauth_token,"<br/>\n\n";

    #oauth_token_secret
    if( !isset($userinfo['access_token']['oauth_token_secret']) ){
        continue;
    }
    $twitter_oauth_token_secret = $userinfo['access_token']['oauth_token_secret'];
    echo 'twitter_oauth_token_secret:',$twitter_oauth_token_secret,"<br/>\n\n";

    db2t($douban_id, $twitter_oauth_token, $twitter_oauth_token_secret);
}

function db2t($douban_id, $twitter_oauth_token, $twitter_oauth_token_secret){
    $last_douban = get_douban_log($douban_id);
    $new_douban = get_douban($douban_id);

    if (empty($douban_id) || empty($twitter_oauth_token) || empty($twitter_oauth_token_secret)) {
        return ;
    }

    if (empty($last_douban)) {
        $last_douban["time"] = 0;
    }

    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitter_oauth_token, $twitter_oauth_token_secret);
    $twitteroauth->host = "https://api.twitter.com/1.1/";
    //var_dump($twitteroauth);

    echo "进入发布页<br/>\n";
    $newst_douban = array();
    foreach ($new_douban as $value) {
        if ($value["time"] > $last_douban["time"]) {
          
            echo "<br/>发布:" . $value["content"] . "<br/>\n";

            $result = $twitteroauth->post('statuses/update', array('status' => $value["content"]));
            if (!empty($result->id_str)) {
                $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
                echo "成功：<a target='_blank' href='{$href}'>地址</a><br/>\n\n";

                $value["twitter_href"] = $href;
            } else {
                set_douban_error_log($douban_id, array("douban" => $value, "result" => $result));
                echo "发布失败<br/>\n\n";
                continue;
            }

            if (empty($newst_douban) || $value["time"] > $newst_douban["time"]) {
                $newst_douban = $value;
            }
        }
    }
    if (empty($newst_douban)) {
        echo "没有新状态发送出去<br/>\n";
    } else {
        set_douban_log($douban_id, $newst_douban);
        echo "发布完成<br/>\n";
    }
}
