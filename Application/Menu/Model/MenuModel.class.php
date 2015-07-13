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
    public function getMenuTree(){
        $data = $this->select();
        foreach ($data as $key => $value) {
            $map['parent_id'] = $value['id'];
            $res = $this->where($map)->select();
            $data[$key]['_son'] = $res;
        }
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
        return $menu;
    }
    /**
     * 保存菜单，通过判断是否存在$data['id']确定是添加或是修改，并返回状态
     * @param array $data form表单提交的数据
     * @return String $state 返回的状态，成功或失败
     * 2015年7月13日20:25:05
     */
    public function saveMenu($data){
        if($data['id'] == null||$data['id'] == ''){
            $this->add($data);
        }else {
            $this->save($data);
        }
        $state = "success";
        return $state;
    }
}
