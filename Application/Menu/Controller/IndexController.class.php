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
    /**
     * 初始化函数，获取菜单列表
     * 调用首页的界面
     * xuao
     * 2015年7月16日18:34:00
     */
    public function indexAction()
    {
        $menuModel = new MenuModel();
        $data = $menuModel->getMenuTree(null, null, 1, 2);

        $menuList = tree_to_list($data,0,'_son');
        
        foreach ($menuList as $key => $value) {
            $menuList[$key]['_url'] = array(
                'add'=>U('addSon?id=' . $value['id']),
                'edit'=>U('edit?id=' . $value['id']),
                'delete'=>U('delete?id=' . $value['id']));
        }
        //var_dump($menuList);
        $url = U('add');
        $this->assign('url',$url);
        $this->assign('data',$menuList);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     * 添加根菜单
     */
    public function addAction(){
        $id = I('get.id',0);
        $data['id'] = $id;
        $title = "添加根菜单";
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);
    }
    /**
     * 添加子菜单，调用的是根菜单的界面
     */
    public function addSonAction(){
        $id = I('get.id',0);
        $data['id'] = $id;
        $title = "编辑菜单";
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);
    }
    /**
     * 编辑菜单，调用的是添加根菜单的界面
     */
    public function editAction(){
        $id = I('get.id');
        $title = "编辑菜单";
        $menuModel = new MenuModel();
        $data = $menuModel->getMenuById($id);
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);
    }
    /***
     * 保存，添加或修改之后，提交到该方法
     * 将获取到的Post数据传递给M层
     */
    public function saveAction(){
        //获取post数据
        $data = I('post.');
        $menuModel = new MenuModel();
        $state = $menuModel->saveMenu($data);
        if($state == "success"){
            $this->success('新增成功', 'index');
        }
    }
    public function deleteAction(){
        $id = I('get.id');
        $menuModel = new MenuModel();
        $state = $menuModel->deleteMenu($id);
        if($state == "success"){
            $this->success('删除成功', 'index');
        }
    }
}