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
    public function addOrEditAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function saveAction(){
        
    }
}

