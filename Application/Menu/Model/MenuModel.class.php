<?php

/*
 * 菜单的Model类
 *
 * @author xuao
 */
namespace Menu\Model;
use Think\Model;
class MenuModel extends Model{
    
    /***
     * 添加子菜单
     * 参数$data array类型 数组下标与数据库字段对应
     * 无返回值
     * 2015年7月9日21:12:10
     * xuao
     * 295184686@qq.com
     */
    public function addSon($data){
        $data['url'] = $data['module'].'/'.$data['controller'].'/'.$data['action'];
        $this->add($data);
        return;
    }
    /***
     * 添加根菜单
     * 参数$data array类型 数组下标与数据库字段对应
     * 无返回值
     * 2015年7月9日21:12:10
     * xuao
     * 295184686@qq.com
     */
    public function addRoot($data){
        $data['url'] = $data['module'].'/'.$data['controller'].'/'.$data['action'];
        $data['parent_id'] = 0;
        $this->add($data);
        return;
    }
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
}
