<?php
/*
 * 后台菜单管理页
 * 添加根菜单:addRootMenuAction
 * 添加子菜单:addSonMenuAction
 * 编辑菜单：editMenuAction
 * 删除菜单：deleteMenuAction
 * creat by xuao 2015年7月1日20:46:26
 * 295184686@qq.com
 */
namespace Menu\Controller;
use Admin\Controller\AdminController;
use Menu\Model\MenuModel;
class IndexController extends AdminController
{
    
    public function indexAction()
    {
        $menuModel = new MenuModel();
        $data = $menuModel->getMenuTree();
        var_dump($data);
        //从数据库中取出菜单信息，加在这，与下边的url的id对应
        $url = array(
            'addRoot'=>U('addRootMenu'),
            'addSon'=>U('addSonMenu?id=$id'),
            'editMenu'=>U('editMenu'));
        $this->assign('url',$url);
        //$this->assign('YZBODY',$this->fetch());
       // $this->display(YZTemplate);
    }
    public function addRootMenuAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addSonMenuAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function editMenuAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     * 保存子菜单函数
     * 与数据库连接，将数据保存到菜单表中
     * 无返回值，保存成功后调用成功界面，并返回index页面
     * 2015年7月9日20:17:15
     * xuao
     * 295184686@qq.com
     */   
    public function saveSonAction(){
        $data = I('post.');
        $menuModel = new MenuModel();
        $menuModel->addSon($data);
        $this->saveOkAction();
    }
    public function saveRootAction(){
        $data = I('post.');
        $menuModel = new MenuModel();
        $menuModel->addRoot($data);
        $this->saveOkAction();
    }

    public function saveOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    public function addOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
}

