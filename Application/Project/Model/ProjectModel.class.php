<?php

/*
 * 项目细节的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Project\Model;
use Think\Model;
class ProjectModel extends Model{
	
    protected $_auto = array(
            array("time","time",3,"function"),
        );
    protected $_validate = array();
    
    private $order = array("time"=>"desc");

	//再取项目细节信息
    public function getListsByIds($lists){
    	$array = array();
    	foreach ($lists as $key => $value) {
    		$map = array();
    		$map['id'] = $value['public_project_detail_id'];
    		$array[$value['public_project_detail_id']] = $this->where($map)->find();
    	}
    	return $array;
    }

    /**
     * 获取项目列表
     * $this->p；当前页
     * $this->pageSize;当前页码大小
     * @return array 二维数组
     */
    public function getLists()
    {
        $map = array();
        $this->totalCount = $this->where($map)->count();
        return $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();
    }

    /**
     * 获取当前用户下的所有项目信息
     * @param  num $userId 用户ＩＤ
     * @return array         二维数组
     */
    public function getListsByUserId($userId)
    {
        $map = array();
        $map['user_id'] = $userId;
        $this->totalCount = $this->where($map)->count();
        return $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();
    }

    public function getListsByUserIdType($userId , $type )
    {
        $map = array();
        $map['user_id'] = $userId;
        $map['type'] = $type;
        $this->totalCount = $this->where($map)->count();
        return $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();   
    }

    /**
     * 获取当前用户当前项目的具体信息
     * @param  int $id     项目ＩＤ
     * @param  int $userId 用户ＩＤ
     * @return array         一组数据。
     * 当查询不到时返回null
     */
    public function getListByIdUserId($id ,$userId)
    {
        $map[id]=$id;
        $map[user_id] = $userId;
        return $this->where($map)->find();
    }
    public function save($userId)
     {
        $data = I('post.');
        $data[user_id] = $userId;
        if($this->create($data)) //将time字段自动存入
        {
            return $this->add($data);
        }
        else
        {
            
            return false;
        }
     } 
}