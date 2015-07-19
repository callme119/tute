<?php
/*
 * 我的工作初始化模块
 * creat by 邓浩洋 2015年7月15日14:44:26
 * 2751111108@qq.com
 */

namespace UserJob\Controller;
use Admin\Controller\AdminController;
use UserJob\Model\UserJobModel;
class IndexController extends AdminController{
    public function indexAction(){
        $user_job = new UserJobModel;
        $user_job->index(1);
        echo "hello";
    }
}