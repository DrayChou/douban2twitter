<?php

require dirname(dirname(__FILE__)).'/init.php';

foreach (glob(USER_DIR."*.twitter.config") as $filename) {
    echo "$filename size " . filesize($filename) . "\n";
    $jsonStr = file_get_contents($filename);
    $userinfo = unserialize($jsonStr);
    var_dump($userinfo);

    $douban_id = isset($userinfo['douban']['name'])?$userinfo['douban']['name']:continue;
    $twitter_oauth_token = isset($userinfo['access_token']['oauth_token'])?$userinfo['access_token']['oauth_token']:continue;
    $twitter_oauth_token_secret = isset($userinfo['access_token']['oauth_token_secret'])?$userinfo['access_token']['oauth_token_secret']:continue;
    db2t($userinfo['douban']['name'], $twitter_oauth_token, $twitter_oauth_token_secret);
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

    echo "进入发布页<br/>\n";
    $newst_douban = array();
    foreach ($new_douban as $value) {
        if ($value["time"] > $last_douban["time"]) {
            $t_2 = file_get_contents($value['link']);
            $text = "";
            if (preg_match("/<p class=\"text\">([\w\W]*?)<\/p>/i", $t_2, $m)) {
                if (preg_match("/<blockquote>([\w\W]*?)<\/blockquote>/i", $t_2, $m2)) {
                    $text = strip_tags(ltrim($m2[1]));
                }
            }
            $value["content"] = $text.$value["content"];
            
            echo "<br/>发布:" . $value["content"] . "<br/>\n";
            $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitter_oauth_token, $twitter_oauth_token);
            $result = $twitteroauth->post('statuses/update', array('status' => $value["content"]));
            if (!empty($result->id_str)) {
                $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
                echo "成功：<a target='_blank' href='{$href}'>地址</a><br/>\n\n";

                $value["twitter_href"] = $href;
            } else {
                set_douban_error_log($douban_id, array("douban" => $value, "result" => $result));
                continue;
            }

            if (empty($newst_douban) || $value["time"] > $newst_douban["time"]) {
                $newst_douban = $value;
            }
        }
    }
    if (empty($newst_douban)) {
        echo "没有新状态需要发送<br/>\n";
    } else {
        set_douban_log($douban_id, $newst_douban);
        echo "发布完成<br/>\n";
    }
}
