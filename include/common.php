<?php

function get_douban_userinfo($douban_id) {
    $douban_rss = 'https://api.douban.com/v2/user/' . $douban_id;
    $jsonStr = file_get_contents($douban_rss);
    $jsonArr = json_decode($jsonStr, true);

    if(DEBUG){
        set_douban_debug_log($douban_id,$jsonArr);
    }
    return $jsonArr;
}

function get_douban($douban_id, $count = 5) {
    $douban_rss = 'https://api.douban.com/shuo/v2/statuses/user_timeline/' . $douban_id . '?count=' . $count;
    $jsonStr = file_get_contents($douban_rss);
    $jsonArr = json_decode($jsonStr, true);

    if(DEBUG){
        set_douban_debug_log($douban_id,$jsonArr);
    }
    
    $douban_rss_list = array();
    if (!empty($jsonArr)) {
        foreach ($jsonArr as $k => $v) {

	        //豆瓣电台
			if( $v['type'] == 'rec_fm' ){
				$id = $v['id'];
				$a = $v['attachments'][0];
				$douban_rss_list[$id] = array(
					'time' => strtotime($v['created_at']),
					'link' => $a['href'],
					'content' => "#DoubanFM ".$v['text']." #NowPlaying ".$v['title'].' -> '.$a['title'],
				);
			}

			//豆瓣说
			if( $v['type'] == null && $v['title'] == "说：" ){
				$id = $v['id'];
				$douban_rss_list[$id] = array(
					'time' => strtotime($v['created_at']),
					'link' => '',
					'content' => "#DoubanShuo ".$v['text'],
				);
			}			
        }
    }

    return $douban_rss_list;
}

function get_douban_log($douban_id) {
    $Str = array();
    $user_file_name = LOG_DIR . $douban_id . ".douban.log";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}

function set_douban_log($douban_id, $douban_str) {
    $user_file_name = LOG_DIR . $douban_id . ".douban.log";
    return file_put_contents($user_file_name, serialize($douban_str));
}

function set_douban_error_log($douban_id, $douban_str) {
    $user_file_name = LOG_DIR . $douban_id . ".douban.error.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, print_r($douban_str, true) . "\n");
    fclose($fh);
    return $result;
}

function set_douban_debug_log($douban_id, $douban_str) {
    $user_file_name = LOG_DIR . $douban_id . ".douban.debug.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, print_r($douban_str, true) . "\n");
    fclose($fh);
    return $result;
}

function set_twitter_config($access_token) {
    //保存下来用户的access_token
    $user_file_name = USER_DIR . $access_token["screen_name"] . ".twitter.config";
    return file_put_contents($user_file_name, serialize($access_token));
}

function get_twitter_config($twitter_id) {
    $Str = array();
    //保存下来用户的access_token
    $user_file_name = USER_DIR . $twitter_id . ".twitter.config";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}

function get_log($log_id) {
    $Str = array();
    $user_file_name = LOG_DIR . $log_id . ".log";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}

function set_log($log_id, $str) {
    $user_file_name = LOG_DIR . $log_id . ".log";
    return file_put_contents($user_file_name, serialize($str));
}

function set_error_log($log_id, $str) {
    $user_file_name = LOG_DIR . $log_id . ".error.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, print_r($str,true) . "\n");
    fclose($fh);
    return $result;
}

function set_debug_log($log_id, $str) {
    $user_file_name = LOG_DIR . $log_id . ".debug.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, print_r($str,true) . "\n");
    fclose($fh);
    return $result;
}