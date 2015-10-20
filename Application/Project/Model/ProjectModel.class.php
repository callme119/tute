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
            array('time','time',3,'function'),
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
     * 获取相关 用户ID TYPE值的列表信息
     * JOIN 项目类别表
     * @param  int $userId 用户ID
     * @param  string $type   类型
     * @return array         二维
     */
    public function getListsJoinProjectCategoryByUserIdType($userId , $type )
    {
        $map = array();
        $map['a.user_id'] = $userId;
        $map['b.type'] = $type;

        $field["a.user_id"] = "user_id";
        $field["a.id"] = "id";
        $field["a.title"] = "title";
        $field["a.project_category_id"] = "project_category_id";
        $field["a.time"] = "time";
        $field["a.cycle_id"] = "cycle_id";

        $field["b.score"] = "score";
        $field["b.score"] = "score";
        
        $field["c.is_finished"] = "is_finished";
        

        $this->alias("a");      //设置表别名
        $this->totalCount = $this->join("left join __PROJECT_CATEGORY__ b on a.project_category_id = b.id left join __WORKFLOW__ c on a.id=c.project_id")->where($map)->count();

        $this->alias("a");  
        $return = $this->where($map)->field($field)->join("left join __PROJECT_CATEGORY__ b on a.project_category_id = b.id left join __WORKFLOW__ c on a.id=c.project_id")->page($this->p,$this->pageSize)->order($this->order)->select();   
        // echo $this->getLastSql();
        return $return;
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

    public function getListById($id)
    {
        $map['id'] = (int)$id;
        $data = $this->where($map)->find();
        return $data;
    }
    
    public function save($userId,$cycleId)
     {
        $data = I('post.');
        $data[user_id] = $userId;
        $data[cycle_id] = $cycleId;
        if($this->create($data)) //将time字段自动存入
        {
            return $this->add();
        }
        else
        {
            
            return false;
        }
     } 
}