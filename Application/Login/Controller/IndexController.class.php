<?php
namespace Login\Controller;
use Think\Controller;
use User\Model\UserModel;
class IndexController extends Controller {
    public function indexAction(){
        $this->assign('remember',cookie('remember'));
        $this->assign('psw',cookie('password'));
        $this->assign('username',cookie('username'));
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
    	//检测是否勾选记住密码，传入cookie信息
    	// if(cookie('remember') != "checked"){
	    // 	if(I('post.remember')== 'on'){
	    // 		cookie('password',I('post.password'));
	    // 		cookie('username',I('post.username'));
	    // 		cookie('remember','checked');
	    // 	}else{
	    // 		cookie('password',null);
	    // 		cookie('username',null);
	    // 		cookie('remember',null);
	    // 	}
    	// }
    	echo cookie('password');
    	$model = new UserModel();
    	if($model->checkUser()){
    		$this->success('登陆成功',U('Admin/Admin/index'));
    	}else{
    		$this->error('用户名密码验证失败',U('Login/Index/index'));
    	}

    }
}
