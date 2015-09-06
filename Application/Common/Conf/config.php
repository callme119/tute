<?php
return array(
    'DEFAULT_MODULE'        => 'Login',  // 默认模块
    'ACTION_SUFFIX'         => 'Action',//设置ACTION前缀为action
    'TAGLIB_PRE_LOAD'       =>  'Yunzhi',   // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔 
    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => 'callme119.mysql.rds.aliyuncs.com', 
    'DB_NAME'   => 'performancems', // 数据库名performancems
    'DB_USER'   => 'performancems', // 用户名performancems
    'DB_PWD'    => 'b2408cac49ed15d67c390dd08a8b0158',  // 密码b2408cac49ed15d67c390dd08a8b0158
    'DB_PORT'   => '3633', // 端口3306
    
    'DB_PREFIX' => 'yunzhi_', // 数据库表前缀
    'UPLOAD_ROOT_PATH' => '/Uploads',//附件上传根路径
    'SESSION_AUTO_START' => true, //开启session
    'PAGE_SIZE' => '20',//分页中，每页显示的条数使用C(PAGE_SIZE)读取;

    'TMPL_PARSE_STRING'  =>array(
         '__PUBLIC__' => __ROOT__ , // 更改默认的/Public 替换规则
         '__JS__'     => __ROOT__ . '/SBAdmin2/js', // 增加新的JS类库路径替换规则
         '__CSS__'     => __ROOT__ .'/SBAdmin2/css', // 增加新的css类库路径替换规则
         '__UPLOAD__' => __ROOT__ .'/Uploads', // 增加新的上传路径替换规则
    )
);