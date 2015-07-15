<?php
/*
 * 所有controller都需要继承该类
 * 在本类中，将进行用户的权限验主，统一设置模板等操作。
 * author:panjie joinpan@gmail.com
 */

namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller{
    private $cssArr = null; //css
    private $jsArr = null; //js
    private $cdnJsArr = null; //js for cdn
    private $ie9CdnJsArr = null; //js for ie9
    
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
    
    public function __construct() {
        parent::__construct();
        $cssArr[] = '/css/bootstrap.min.css';
        $cssArr[] = '/css/font-awesome.min.css';
        $cssArr[] = '/css/ionicons.min.css';
        $cssArr[] = '/css/morris/morris.css';
        $cssArr[] = '/css/jvectormap/jquery-jvectormap-1.2.2.css';
        $cssArr[] = '/css/fullcalendar/fullcalendar.css';
        $cssArr[] = '/css/daterangepicker/daterangepicker-bs3.css';
        $cssArr[] = '/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css';
        $cssArr[] = '/css/AdminLTE.css';
        $cssArr[] = '/css/select2/select2.min.css';
        
        $jsArr[] = '/js/jquery-2.1.4.min.js';
        $jsArr[] = '/js/jquery-ui-1.10.3.min.js';   
        $jsArr[] = '/js/bootstrap.min.js';
        $jsArr[] = '/js/plugins/morris/morris.min.js';
        $jsArr[] = '/js/plugins/sparkline/jquery.sparkline.min.js';
        $jsArr[] = '/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js';
        $jsArr[] = '/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js';
        $jsArr[] = '/js/plugins/fullcalendar/fullcalendar.min.js';
        $jsArr[] = '/js/plugins/jqueryKnob/jquery.knob.js';
        $jsArr[] = '/js/plugins/daterangepicker/daterangepicker.js';
        $jsArr[] = '/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js';
        $jsArr[] = '/js/plugins/iCheck/icheck.min.js';
        $jsArr[] = '/js/AdminLTE/app.js';
        $jsArr[] = '/js/AdminLTE/dashboard.js';
        $jsArr[] = '/js/select2/select2.min.js';
        
        
        $cdnJsArr[] = '//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js';
        
        $ie9CdnJsArr[] = 'https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js';
        $ie9CdnJsArr[] = 'https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js';
        
        $this->assign('ie9CdnJsArr',$ie9CdnJsArr);
        $this->assign('cdnJsArr',$cdnJsArr);
        $this->assign('cssArr',$cssArr);
        $this->assign('jsArr',$jsArr);
        $this->assign('YZBodyClass','skin-blue wysihtml5-supported  pace-done');
        
        //开始进行菜单访问权限判断
        $url = $this->_getUrl();
        $isjump = $this->_checkUrl($url);
        if(!$isjump){
            $this->_jumpUrl();
        }        
        $headerTpl = T('Admin@Admin/header');
        $this->assign('header',$this->fetch($headerTpl));
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
    }
    /**
     * 获取当前的url
     * @return 数组类型 返回$url，包括模块名，控制器名，和方法名
     */
    private function _getUrl(){
        $url = array();
        $url['module'] = C('MODULE_NAME');
        $url['controller'] = C('COTROLLER_NAME');
        $url['action'] = C('ACTION_NAME');
        return $url;
    }
    private function _checkUrl($url){
        return true;
    }
    private function _jumpUrl(){
        $url = U('Admin/Index/fail');
        redirect($url);
    }
}

