<?php

function objectsIntoArray($arrObjData, $arrSkipIndices = array()) {
    $arrData = array();

    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }

    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}

function get_douban($douban_id) {
    $douban_rss = 'http://www.douban.com/feed/people/' . $douban_id . '/miniblogs';
    $xmlStr = file_get_contents($douban_rss);
    $xmlObj = simplexml_load_string($xmlStr);

    if(DEBUG){
        set_douban_debug_log($douban_id,$xmlObj);
    }
    
    $douban_rss_list = array();
    if (!empty($xmlObj->channel->item)) {
        foreach ($xmlObj->channel->item as $k => $v) {
            $douban_book_base_url = "http:\/\/book.douban.com\/subject\/";
            if ($v->link == $v->guid || preg_match($douban_book_base_url, $v->link)) {
				/*
                $t_2 = file_get_contents($v->link);
				$text = "";
                if (preg_match("/<p class=\"text\">([\w\W]*?)<\/p>/i", $t_2, $m)) {

                    $text = "";
                    if (preg_match("/<blockquote>([\w\W]*?)<\/blockquote>/i", $t_2, $m2)) {
                        $text = strip_tags(ltrim($m2[1]));
                    }
                }
				*/
				
				$time = strtotime($v->pubDate);
				$douban_rss_list[$time]["time"] = $time;
				$douban_rss_list[$time]["link"] = strval($v->link);
				$douban_rss_list[$time]["content"] = $text . " #douban #NowPlaying " . strip_tags(ltrim(str_ireplace('We_Get推荐','',$v->title)));
				
				/*
				echo "<pre>";
				var_dump($douban_rss_list[$time]);
				echo "</pre>";
				*/
            }
			
        }
    }

    return $douban_rss_list;
}

function get_douban_log($douban_id) {
    $Str = array();
    $user_file_name = DATA_DIRECTORY . $douban_id . ".douban.log";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}

function set_douban_log($douban_id, $douban_str) {
    $user_file_name = DATA_DIRECTORY . $douban_id . ".douban.log";
    return file_put_contents($user_file_name, serialize($douban_str));
}

function set_douban_error_log($douban_id, $douban_str) {
    $user_file_name = DATA_DIRECTORY . $douban_id . ".douban.error.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, serialize($douban_str) . "\n");
    fclose($fh);
    return $result;
}

function set_douban_debug_log($douban_id, $douban_str) {
    $user_file_name = DATA_DIRECTORY . $douban_id . ".douban.debug.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, serialize($douban_str) . "\n");
    fclose($fh);
    return $result;
}

function set_twitter_config($access_token) {
    //保存下来用户的access_token
    $user_file_name = DATA_DIRECTORY . $access_token["screen_name"] . ".twitter.config";
    return file_put_contents($user_file_name, serialize($access_token));
}

function get_twitter_config($twitter_id) {
    $Str = array();
    //保存下来用户的access_token
    $user_file_name = DATA_DIRECTORY . $twitter_id . ".twitter.config";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}