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
        $data = $menuModel->getMenuTree(null,null,null,2);
        $menuList = $this->_treeToList($data);
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
    public function addAction(){
        $id = I('get.id',0);
        $data = array();
        $data['id'] = $id;
        $title = "添加根菜单";
        $this->assign('title',$title);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addSonAction(){
        $id = I('get.id');
        $data = array();
        $data['id'] = $id;
        $title = "添加子菜单";
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);
    }
    public function editAction(){
        $id = I('get.id');
        $title = "编辑菜单";
        $menuModel = new MenuModel();
        $data = $menuModel->getMenuById($id);
        $data['edit'] = 1;
        $data['id'] = $id;
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);
    }

    public function saveAction(){
        $data = I('post.');
        if($data['edit'] == null || $data['edit'] == ''){
            $data['id'] = null;
        }
        $menuModel = new MenuModel();
        $state = $menuModel->saveMenu($data);
        if($state == "success"){
            $this->success('新增成功', 'index');
        }
    } 
    
    /**
     * 将树形结构转化为list列表
     * @param type $tree 数组，要转化成List的树
     * @param type $i  树的层级
     * @return type 返回list列表
     * creat by pan
     */
    private function _treeToList($tree,$i = 0){
        $list = array();
        foreach($tree as $key => $value)
        {
            $value['level'] = $i;
            $list[] = $value;
            if(is_array($value['_son']))
            {
                $i++;
                $list = array_merge($list,$this->_treeToList($value['_son'],$i));
                $i--;
            }
        }
        return $list;
    }
}

