<?php
namespace Login\Controller;
use Think\Controller;
use User\Model\UserModel;
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

    //对用户名密码进行判断
    public function loginAction(){
    	$model = new UserModel();
    	if($model->checkUser()){
    		$this->success('登陆成功',U('Admin/Admin/index'));
    	}else{
    		$this->error('用户名密码验证失败',U('Login/Index/index'));
    	}

    }
}
