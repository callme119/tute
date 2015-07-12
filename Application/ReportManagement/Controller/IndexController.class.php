<?php
namespace ReportManagement\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController {
    public function indexAction() {
        $this->assign('css',$this->fetch(T('indexCss')));
        $this->assign('YZBODY', $this->fetch());
        $this->display(YZTemplate);
    }
}