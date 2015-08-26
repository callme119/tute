<?php

namespace PositionManagement\Controller;

use Admin\Controller\AdminController;

class IndexController extends AdminController {

    public function indexAction() {
        $this->assign('YZBODY', $this->fetch());
        $this->display(YZTemplate);
    }
    public function addAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }

}