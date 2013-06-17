<?php
require dirname(dirname(__FILE__)).'/init.php';

define('WEATHER_ID', 'cc_weather');

$userinfo = get_twitter_config(WEATHER_ID);
if( empty($userinfo) ){
	echo "请先认证发布帐号 {WEATHER_ID} <br/>\n";
	die();
}

$last = get_log(WEATHER_ID);
$newst = array();
$to_twitters = array(
    'We_Get' => '上海',
    'gomixo' => '上海',
    'lamiauu' => '上海',
    'heejunjin' => '上海',
//    'xiaoxiao1989' => '上海',
    'dinosaurshmily' => '上海',
    '362227' => '东莞',
);
$weather_url = array(
    '上海' => 'http://wap.weather.com.cn/wap/weather/101020100.shtml',
    '东莞' => 'http://wap.weather.com.cn/wap/weather/101281601.shtml',
);


$twitter_config = $userinfo['access_token'];
if (empty($twitter_config)) {
    echo "请先认证发布帐号 {WEATHER_ID} <br/>\n";
	die();
}
$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitter_config['oauth_token'], $twitter_config['oauth_token_secret']);
$twitteroauth->host = "https://api.twitter.com/1.1/";
echo "进入发布页<br/>\n";

//抓取天气预报
foreach ($weather_url as $city => $url) {
    if (empty($last['weather_log'][$city]["time"])) {
        $last['weather_log'][$city]["time"] = 0;
    }
    $html = file_get_contents($url);
    if(preg_match("/<h3>([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2})时发布<\/h3>/i", $html, $m) != 1){
        echo "页面访问失败，没有拿到更新时间<br/>\n";
        die();
    }
    $update_time = strtotime($m[1].':00');
    if ($update_time <= $last['weather_log'][$city]["time"]) {
        echo date("Y-m-d H:i:s",$update_time)," 以来, {$city} 没有新天气状态需要发送<br/>\n";
        continue;
    }

    preg_match_all("/<dd>([\w\W]*?)<\/dd>[\s]*<dt>([\w\W]*?)<\/dt>/i", $html, $new_list);
    if(!empty($new_list) && !empty($new_list[1][0])){
        $newst['weather_log'][$city]['time'] = $update_time;
        $newst['weather_log'][$city]['weather'] = "#{$city}天氣預報 " . preg_replace("/\&nbsp\;/i", " ", strip_tags(ltrim($new_list[1][0]))) . ' '.preg_replace("/\&nbsp\;/i", " ", strip_tags(ltrim($new_list[2][0])));
    }
}

//检查有没有天气预报
set_debug_log(WEATHER_ID, array("newst" => $newst, "last" => $last));
if(empty($newst)){
    echo date("Y-m-d H:i:s",$update_time)," 以来,没有新状态需要发送<br/>\n";
    die();
}

//发布天气预报
foreach ($to_twitters as $tid => $city) {
    if(isset($newst['weather_log'][$city]['weather'])){
        $newst['twitter_log'][$tid]["content"] = 'cc @'.$tid.' '.$newst['weather_log'][$city]['weather'];
        echo "<br/>发布:" . $newst['twitter_log'][$tid]["content"] . "<br/>\n";
        $result = $twitteroauth->post('statuses/update', array('status' => $newst['twitter_log'][$tid]["content"]));
        $newst['twitter_log'][$tid]["result"] = $result;
        if (!empty($result->id_str)) {
            $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
            $newst['twitter_log'][$tid]["twitter_href"] = $href;
            echo "成功：<a target='_blank' href='{$href}'>地址</a><br/>\n\n";
        } elseif ($result->error == 'Status is a duplicate.') {
            echo "已经发布过这条讯息：{$newst['twitter_log'][$tid]['content']}";
        } else {
            set_error_log(WEATHER_ID, array("twitter_id" => $tid, "error_log" => $newst['twitter_log'][$tid]));
            echo "失败：{$result->error}\n\n";
            continue;
        }
    }
}

set_log(WEATHER_ID, $newst);
echo "\n\n发布完成<br/>\n\n";

