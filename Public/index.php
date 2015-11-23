<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('APP_DEBUG',true);

//define('BIND_MODULE','Login');


// 定义应用目录
define('APP_PATH','./../Application/');

define('RUNTIME_PATH','./../Runtime/');
// 引入ThinkPHP入口文件
require './../ThinkPHP/ThinkPHP.php';