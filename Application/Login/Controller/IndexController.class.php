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

    }

    //对用户名密码进行判断
    public function loginAction(){
    	//检测是否勾选记住密码，传入cookie信息
        $data = cookie('remember');
    	if(empty($data)){
	    	if(I('post.remember') == 'on'){
	    		cookie('password',I('post.password'),30*24*60*60);
	    		cookie('username',I('post.username'),30*24*60*60);
	    		cookie('remember','checked',30*24*60*60);
	    	}
    	}else{
            if(I('post.remember') != 'checked'){
                cookie('password',null);
                cookie('username',null);
                cookie('remember',null);
            }
        }
        //验证用户名密码
    	$model = new UserModel();
    	if($model->checkUser()){
    		$this->success('登陆成功',U('Admin/Index/index'));
    	}else{
    		$this->error('用户名密码验证失败',U('Login/Index/index'));
    	}

    }

    //注销功能
    public function cancelAction(){
        
    }
}
