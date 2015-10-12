<?php
/*
 * 所有controller都需要继承该类
 * 在本类中，将进行用户的权限验主，统一设置模板等操作。
 * author:panjie joinpan@gmail.com
 */

namespace Admin\Controller;
use Think\Controller;
use Menu\Model\MenuModel;
use User\Model\UserModel;
use Admin\Logic\AdminLogic;
use WorkflowLog\Logic\WorkflowLogLogic;        //工作流日志
class AdminController extends Controller{
    private $cssArr = null; //css
    private $jsArr = null; //js
    private $cdnJsArr = null; //js for cdn
    private $ie9CdnJsArr = null; //js for ie9
    protected $p = 1;
    protected $pageSize = 20;
    
    public function addCss($css){
        $this->cssArr[] = $css;
    }
    
    public function addJs($js){
        $this->jsArr[] = $js;
    }
    
    public function addCdnJs($cdnJs){
        $this->cdnJsArr[] = $cdnJs;
    }
    
    public function addIe9CdnJs($ie9CdnJs){
        $this->ie9CdnJsArr[] = $ie9CdnJs;
    }
     /**
     * 判读是否存在该菜单
     * 如果不存在，跳转404界面
     */
    public function _empty(){
        try
        {
            if(APP_DEBUG)
            {
                if(is_string($this->error))
                {
                    throw new \Think\Exception($this->error,1);
                }
                if(is_object($this->error))
                {
                    throw $this->error;
                }
            }
            $this->assign("e",$this->error);
            $this->assign("YZBODY",$this->fetch(T("Admin@Admin/fail")));     
            $this->display(YZTemplate);
            exit();
        }
        catch(\Think\Exception $e)
        {
            throw $e;
        }
        
    }
    public function __construct() {
        parent::__construct();

        //定义公共模板
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
        try
        {
            //判断是否已经登录
            $userId = get_user_id();

            //当前页
            $this->p = (int)I('get.p') ? (int)I('get.p') : 1;

            //分页多少条
            $this->pageSize = C("PAGE_SIZE") ? C("PAGE_SIZE") : $this->pageSize;

            //获取用户基础信息
            $userM = new UserModel;
            $user = $userM->getUserById($userId);

            //开始进行菜单访问权限判断
            //1.获取用户点击或输入的url
            $url = $this->_getUrl();
            //3.判读该url是否有该菜单（判断应用位运算）
            //缓存初始化
            // S(array('type'=>'File','expire'=>60));
            //获取该用户可见的菜单列表并传递给给Left菜单栏
            
            $adminLogic = new AdminLogic();
            $data = $adminLogic -> getPersonalMenuListByUserId($userId);
            $data = $this->_addMenuActive($data, $url, '_son');
            //3.将获取到的信息传递给V层
            $this->assign('leftMenu',$data);
            
            //获取当前菜单信息
            $menuModel = new MenuModel;
            $currentMenu = $menuModel ->getMenuByUrl($url);

            //2.判读该用户是否有该权限
            $isJump = $adminLogic->checkUrl($currentMenu,$userId);

            if(!$isJump){
                $this -> error = "你没有该权限,
                如果你是开发人员的话，请在菜单管理中添加相关菜单";
                throw new \Think\Exception($this->error,1);
            }

            $WorkflowLogL = new WorkflowLogLogic();
            //取当前用户待办工作个数
            $_UserTodoCount = $WorkflowLogL->getTodoCountByUserId($userId);

            //取当用户在工作工作个数
            $_UserDoingCount = $WorkflowLogL->getDoingCountByUserId($userId);

            $this->assign("_UserTodoCount",$_UserTodoCount);
            $this->assign("_UserDoingCount",$_UserDoingCount);
            $this->assign('currentMenu',$currentMenu[0]);
            $this->assign("user",$user);         
        }
        catch(\Think\Exception $e)
        {
            //非测试环境，则跳转到我们自己的页面
            if(!APP_DEBUG)
            {
                $tpl = T("Admin@Admin/index");
                define('YZTemplate', $tpl);
            }
            $this->error = $e;
            $this->_empty();
        }


    }
    /**
     * 获取url信息
     * 无参数值
     * @return type 返回值是数组类型，包括module,controller,action
     * 2015年7月18日19:20:06
     */
    private function _getUrl(){
        //获取url
        $url = array();
        $url['module'] = MODULE_NAME;
        $url['controller'] = CONTROLLER_NAME;
        $url['action'] = ACTION_NAME;
        return $url;
    }
    /**
     * 
     * @param array $arr 传入数组
     * @param array $url url数组
     * @param string $type 数组下级数组下标值
     * @return arr 添加了active的数组
     */

    private function _jumpUrl(){
        $url = U('Fail/Index/fail');
        redirect($url);
    }
    private function _addMenuActive($arr,$url,$type){
        //$arr = change_key($arr, 'id');
        $menuModel = new MenuModel();
        $res = $menuModel->getMenuByUrl($url);
        $id = $res[0]['id'];
        $parentId = $res['parent_id'];
        foreach (arr as $key => $value) {
            if($value['id'] == $id || $value['id'] == $parentId){
                $arr['isActive'] = [1];
            }
            foreach ($value[$type] as $k => $v) {
                if($value['id'] == $id || $value['id'] == $parentId){
                $value[$type]['isActive'] = [1];
                }
            }
        }
        return $arr;
    }
}
