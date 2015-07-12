<?php
namespace Login\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function indexAction(){
        
        $this->assign('css',$this->fetch(T('indexCss')));
        $this->assign('js',$this->fetch(T('indexJs')));
        $login = $this->fetch(T('index'));
        $this->show($index);
//        $this->assign('YZBODY');
        //$this->display("login");
        //echo("111");
    }
}
