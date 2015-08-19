<?php
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController {
    public function indexAction(){
        //$this->assign('YZBODY',$this->fetch(T('scientificResearch')));
        
        //$this->show('scientificResearch');
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
        }
        public function savedAction() {
            $this->assign('YZBODY',$this->fetch());
            $this->display(YZTemplate);
        }
        public function auditedAction() {
            $this->assign('YZBODY',$this->fetch());
            $this->display(YZTemplate);
        }
        public function addAction() {
            $this->assign('YZBODY',$this->fetch());
            $this->display(YZTemplate);
        }
        public function auditprocessAction() {
            $this->assign('YZBODY',$this->fetch());
            $this->display(YZTemplate);
        }
        public function submittedAction() {
            $this->assign('YZBODY',$this->fetch());
            $this->display(YZTemplate);
        }
        public function auditsuggestionAction() {
            $this->assign('YZBODY',$this->fetch());
            $this->display(YZTemplate);
        }
}
