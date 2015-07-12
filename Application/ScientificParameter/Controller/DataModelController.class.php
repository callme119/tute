<?php
/**
 * 数据模型.
 * 由于需要添加的名细，所包含的共有属性很难提练。
 * 同时，考虑到以后系统的升级，主要是算法的升级。
 * 引入：数据模型。实现原理参考：onethink数据模型。
 * author:pan jie
 * email:3792535@qq.com
 * create date:2015.07.06
 */
namespace ScientificParameter\Controller;
use Admin\Controller\AdminController;
class DataModelController extends AdminController{
    /**
     * 初始化数据模型列表。
     */
    public function indexAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
        
    }
}
