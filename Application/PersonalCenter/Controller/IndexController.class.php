<?php

namespace PersonalCenter\Controller;

use Admin\Controller\AdminController;

class IndexController extends AdminController {

    public function indexAction() {
        
        $this->assign('css',$this->fetch(T('indexCss')));
//        $index = $this->fetch(T('index'));
//        $this->show($index);
        $this->assign('YZBODY', $this->fetch());
        $this->display(YZTemplate);
    }

}
