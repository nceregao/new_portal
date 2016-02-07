<?php


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'news_portal');


define('PATH_ROOT', dirname(__FILE__).'/');
define('PATH_CONTENT', PATH_ROOT.'content/');
define('PATH_TEMPLATE', PATH_ROOT.'resources/template/');


if ( !file_exists(PATH_CONTENT) ) die("ERROR: ".PATH_CONTENT." folder not exists\n");