<?php
namespace Config\Controller;
use Admin\Controller\AdminController;
use Model\Config;

class IndexController extends AdminController {
    private $name = 'closing_time';     // 配置信息的设置值
    private $Config = null;             // 配置对象

    public function __construct() {
        $this->Config = new Config($this->name);
        if (is_array($this->Config->getData('value'))) {
            throw new Exception("未获取到系统的开放的截止日期, 请检查config表中是否对closing_time进行了设置", 1);
        } 
        parent::__construct();
    }

    /**
     * 着陆页
     * @return   Html                   
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T09:11:23+0800
     */
    public function indexAction() {
        $this->assign('Config', $this->Config);
        $this->assign("YZBODY",$this->fetch());
        $this->display(YZTemplate);
    }

    /**
     * 保存更新
     * @return   html status 301 | 302              
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T09:11:44+0800
     */
    public function saveAction() {
        $closingTime = I('post.closing_time');
        if (empty($closingTime)) {
            throw new Exception('未获取到closing_time字段', 1);
        }

        // 更新数据
        $status = $this->Config->updateValueFromDateString($closingTime);

        $this->success('操作成功', U('index'));
    }
}