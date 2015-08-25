<?php
return array(
    'DEFAULT_MODULE'        => 'Login',  // 默认模块
    'ACTION_SUFFIX'         => 'Action',//设置ACTION前缀为action
    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
<<<<<<< HEAD
    'DB_HOST'   => 'callme119.mysql.rds.aliyuncs.com', 
    'DB_NAME'   => 'performancems', // 数据库名performancems
    'DB_USER'   => 'performancems', // 用户名performancems
    'DB_PWD'    => 'b2408cac49ed15d67c390dd08a8b0158',  // 密码b2408cac49ed15d67c390dd08a8b0158
    'DB_PORT'   => '3633', // 端口3306
=======
    'DB_HOST'   => 'localhost', // 服务器地址www.callme119.com
    'DB_HOST'   => 'callme119.com', 
    'DB_NAME'   => 'performancems', // 数据库名performancems
    'DB_USER'   => 'performancems', // 用户名performancems
    'DB_PWD'    => 'b2408cac49ed15d67c390dd08a8b0158',  // 密码b2408cac49ed15d67c390dd08a8b0158
    'DB_PORT'   => '3306', // 端口3306
>>>>>>> origin/master
    'DB_PREFIX' => 'yunzhi_', // 数据库表前缀
    'UPLOAD_ROOT_PATH' => '/Uploads',//附件上传根路径
    'SESSION_AUTO_START' => true, //开启session
    'PAGE_SIZE' => '20',//分页中，每页显示的条数使用C(PAGE_SIZE)读取;
);