<?php

//灵菲茜语推荐《说谎》 - 林宥嘉

require 'config.php';
require 'common.php';
require 'twitteroauth/twitteroauth.php';

$title = '灵菲茜语推荐《说谎》 - 林宥嘉';
echo DOUBAN_NICKNAME.'推荐','<br/>';
echo $text . " #NowPlaying " . strip_tags(ltrim(str_ireplace(DOUBAN_NICKNAME.'推荐','',$title))) . " in Douban.FM";

$douban = get_douban(DOUBAN_ID);
echo "<pre>";
var_dump($douban);
echo "</pre>";