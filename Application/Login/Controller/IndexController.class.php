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
    	switch ($model->checkUser()) {
            case '1':
                //根据post的用户名取出用户信息，再将id与name存入session
                $list = $model->getUserInfoByName(I('post.username'));
                session('user_id',$list['id']);
                session('user_name',$list['username']);
                //登录成功后跳转
                redirect_url(U('Admin/Index/index'));
                break;
            case '0':
                $this->error('用户名密码错误',U('Login/Index/index'));
                break;
            case '2':
                $this->error('无此用户名',U('Login/Index/index'));
        }       
    }

    //注销功能
    public function cancelAction(){
        session('user_id',null);
        session('user_name',null);
        $this->success('注销成功',U('Login/Index/index'));
    }

    //直接登录
    public function loginDirectAction(){
        
        if(APP_DEBUG)
        {
            session('user_id',5);
            redirect_url(U('Admin/Index/index'));
        }else{
            exit();
        }

    }

    public function checkAjaxAction(){
        $data = array();
        $model = new UserModel();
        $username = I('get.username');
        $password = I('get.password');
        switch ($model->checkUser($username,$password)) {
            case '1':
                //根据post的用户名取出用户信息，再将id与name存入session
                $list = $model->getUserInfoByName(I('post.username'));
                session('user_id',$list['id']);
                session('user_name',$list['username']);
                //登录成功后跳转
                $data['state'] = "success";
                $this->ajaxReturn($data);
                break;
            case '0':
                $data['state'] = "error";
                $data['msg'] = "用户名密码错误" ;
                $this->ajaxReturn($data);
                break;
            case '2':
                $data['state'] = "error";
                $data['msg'] = "无此用户名" ;
                $this->ajaxReturn($data);
                break;
        }
    }
}
