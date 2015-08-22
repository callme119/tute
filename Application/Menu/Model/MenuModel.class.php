<?php

/*
 * 菜单的Model类
 *
 * @author xuao
 * 295184686@qq.com
 */
namespace Menu\Model;
use Think\Model;
class MenuModel extends Model{
    /**
     * 获取菜单树方法
     * 
     * @return array
     */
    public function getMenuTree($parentId,$isDevelop,$isShow = 1,$layer){
        $map = array();
        $level = isset($layer)?$layer:1;
        $map['parent_id'] = isset($parentId)?$parentId:0;
        if(APP_DEBUG == false)
        {
            $map['development'] = 1;
        }
        if($isShow == 1)
        {
            $map['show'] = $isShow;
        }
        $data = $this->_getMenuList($map, $level);
        return $data;
    }
    /**
     * 通过url获取菜单信息，并以数组类型返回
     * @param array $url 包括Module,controller,action获取url信息
     * 返回值 $menu 数组类型
     * xuao 2015年7月13日20:06:40
     * 295184686@qq.com
     */
     
    public function getMenuByUrl($url){
        $map = array();
        $map['mouble'] = $url['mouble'];
        $map['controller'] = $url['controller'];
        $map['action'] = $url['action'];
        $menu = $this->where($map)->select();
        return $menu;
    }
    /**
     * 通过id获取菜单信息，以数组形式返回
     * @param String $id 菜单Id
     * @return array  $menu 菜单信息
     * 2015年7月13日20:09:37
     */
    public function getMenuById($id){
        $map['id'] = $id;
        $menu = $this->where($map)->find();
        $menu['edit'] = 1;
        return $menu;
    }
    /**
     * 保存菜单，通过判断是否存在$data['id']确定是添加或是修改，并返回状态
     * @param array $data form表单提交的数据
     * @return String $state 返回的状态，成功或失败
     * 2015年7月13日20:25:05
     */
    public function saveMenu($data){
        //拼接url信息
        $data['url'] = $data['module'].'/'.$data['controller'].'/'.$data['action'];
        if($data['edit'] == null || $data['edit'] == ''){
            $data['id'] = null;
            $this->add($data);
        }else {
            $this->save($data);
        }
        $state = "success";
        return $state;
    }
    public function deleteMenu($id)
    {
        $map['id'] = $id;
        return $this->where($map)->delete();
    }
    /**
     * @param  [array] $where查询条件
     * @param  [int] $layer 查询层级
     * @param  integer 用于返回第几层
     * @return [type] 带有数据信息的数组
     */
    private function _getMenuList($where,$layer){
        $menuList = $this->where($where)->select();

        //如果结果为空,则返回false
        if( $menuList == null)
            return false;

        //判断层级
        if($layer--)
        {
            foreach($menuList as $key => $value)
            {
                $map = $where;
                $map['parent_id'] = $value['id'];
                $son = $this->_getMenuList($map,$layer);
                if($son !== false)
                {
                    $menuList[$key]['_son'] = $son;
                }
            }
        }   

        return $menuList;
    }
    public function checkMenu($url) {
        $boolean = $this->where($url)->count();
        if($boolean){
            return TRUE;
        }  else {
            return FALSE;
        }
    }
    public function getParentMenuById($id){
        $map['id'] = $id;
        $pid = $this->field('parent_id')->where($map)->find();
        $map1['id'] = $pid;
        $data = $this->where($map1)->find();
        return $data;
    }
}
