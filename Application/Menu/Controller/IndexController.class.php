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
        foreach ($data as $key => $value) {
            $data['_url'] = array(
                'add'=>U('addOrEdit?a=add&id='.$value['id']),
                'edit'=>U('addOrEdit?a=edit&id='.$value['id']),
                'delete'=>U('delete?a=add&id='.$value['id']),);
        }
        //var_dump($data);
        //从数据库中取出菜单信息，加在这，与下边的url的id对应
        $url = U('addOrEdit?a=addroot');
        $this->assign('url',$url);
        $this->assign('data',$data);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     * 调用添加，编辑菜单界面
     * 根据get的a的值判断具体操作是什么，并传入title和相应的数据
     * 无参数，无返回值
     * 2015年7月14日20:56:29
     */
    public function addOrEditAction(){
        $action = I('get.a');
        switch ($action){
            case "add":
                $title = "添加子菜单";
                break;
            case "addroot":
                $title = "添加根菜单";
                break;
            case "edit":
                $title = "编辑菜单";
                $id = I('get.id');
                $menuModel = new MenuModel();
                $data = $menuModel->getMenuById($id);
                $this->assign('data',$data);
                break;
        }
        $this->assign('title',"添加根菜单低调低调");
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function saveAction(){
        
    }
}

