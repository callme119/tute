<?php
/**
 * 项目表
 * author: panjie
 * 3792535@qq.com
 */
namespace Project\Logic;
use Project\Model\ProjectModel;
class ProjectLogic extends ProjectModel
{
	protected $tableName = '';	//数据表后缀
	protected $order = 'time desc';
	//通过后缀设置数据表
	public function setTableSuffix($suffix)
	{
		$tableName = '__project_detail_' . $suffix . '__';
		$this->tableName = strtoupper($tableName);
	}

	//通过项目的详情ID来获取Lists数据
	public function getListByIdFromSuffixTable($projectDetailId = 0)
	{
		if($this->tableName === null)
		{
			$this->error = "未传入数据表后缀";
			return false;
		}

		if($this->tableName === '')
		{
			$this->error = "未设置数据表后缀";
			return null;
		}
		
		//查询并返回数据.为避免因数据表错误而导致的异常，必须进行异常处理。
		$map['id'] = $projectDetailId;
		try
		{
			$return = $this->table("$this->tableName")->where($map)->find();
			return $return;
		}
		catch(\Think\Exception $e)
		{
			$this->error = "数据查询出现错误，错误信息:" . $e->getMessage();
			return null;
		}	
	}

	public function getListByProjectDetailIdSuffix($projectDetailId = 0, $suffix = '')
	{
		//设置表后缀
		$this->setTableSuffix($suffix);

		//查询出数据
		return $this->getListByIdFromSuffixTable($projectDetailId);
	}

	/**
	 * 返回同一项目类别的数据
	 * @param  int $projectCategoryId 项目类别ID
	 * @return array                    二维数组
	 */
	public function getListsByProjectCategoryId($projectCategoryId)
	{
		$map['project_category_id'] = $projectCategoryId;
		$data = $this->where($map)->select();
		return $data;
	}

	/**
	 * 根据周期ID获取相关数据信息
	 * @param  int $cycleId 周期ID
	 * @return array          二维数据或empty
	 */
	public function getListsByCycleId($cycleId)
	{
		$cycleId = (int)$cycleId;
		$map['cycle_id'] = $cycleId;
		$this->totalCount = $this->where($map)->count();
		return $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();
	}

	/**
	 * 根据周期ID 类目类别的类型 获取相关数据信息
	 * @param  int $cycleId 周期ID
	 * @param string $type 项目类别类型
	 * @return array          二维数据或empty
	 */
	public function getListsByCycleIdType($cycleId , $type)
	{
		$cycleId = (int)$cycleId;
		$type = trim($type);

		$map['a.cycle_id'] = $cycleId;
		$map['b.type'] = $type;

		$field['a.id'] = "id";
		$field['a.title'] = "title";
		$field['a.project_category_id'] = "project_category_id";
		$field['a.time'] = "time";
		$field['a.user_id'] = "user_id";

		$this->alias("a");
		$this->totalCount = $this->where($map)->join("left join __PROJECT_CATEGORY__ b on a.project_category_id = b.id")->count();

		$this->alias("a");
		return $this->where($map)->field($field)->join("left join __PROJECT_CATEGORY__ b on a.project_category_id = b.id")->page($this->p,$this->pageSize)->order($this->order)->select();
	}

	/**
	 * 根据周期ID获取相关数据信息
	 * @param  int $cycleId 周期ID
	 * @return array          二维数据或empty
	 */
	public function getAllListsByCycleId($cycleId)
	{
		$cycleId = (int)$cycleId;
		if($cycleId == 0)
		{
			$this->error = "传入的cycleId值为0";
			return flase;
		}
		$map['cycle_id'] = $cycleId;
		return $this->where($map)->select();
	}

	public function getListsByUserIdCycleId($userId , $cycleId)
	{
		$userId = (int)$userId;
		$cycleId = (int)$cycleId;
		$map['user_id'] = $userId;
		$map['cycle_id'] = $cycleId;
		$this->totalCount = $this->where($map)->count();
		$return = $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();
		// echo $this->getLastSql();
		return $return;

	}
	
	/**
	 * 返回 特定用户 特定周期 特别类目类型 下的当前页记录
	 * @param  int $userId   用户ID
	 * @param  int $cycleId 周期ID
	 * @param  string $type    项目类别类型
	 * @return array          二维
	 */
	public function getListsByUserIdCycleIdType($userId , $cycleId , $type)
	{
		$userId = (int)$userId;
		$cycleId = (int)$cycleId;
		$type = trim($type);

		$map['a.user_id'] = $userId;
		$map['a.cycle_id'] = $cycleId;
		$map['b.type'] = $type;

		$field['a.id'] = "id";
		$field['a.title'] = "title";
		$field['a.project_category_id'] = "project_category_id";
		$field['a.time'] = "time";
		$field['a.user_id'] = "user_id";

		$this->alias("a");
		$this->totalCount = $this->where($map)->join("left join __PROJECT_CATEGORY__ b on a.project_category_id = b.id")->count();

		$this->alias("a");
		$return = $this->where($map)->field($field)->join("left join __PROJECT_CATEGORY__ b on a.project_category_id = b.id")->page($this->p,$this->pageSize)->order($this->order)->select();
		 // echo $this->getLastSql();
		return $return;
	}

	public function getListById($id)
	{
		$id = (int)$id;
		$map['id'] = $id;
		$return = $this->where($map)->find();
		if($return == null)
		{
			$this->error = "ID为$id的纪录未找到";
			return false;
		}
		return $return;
	}

	public function deleteById($id)
	{
		$id = (int)$id;
		$map['id'] = $id;

		$return = $this->where($map)->find();
		if($return == null)
		{
			$this->error = "ID为$id的纪录未找到";
			return;
		}
		$this->where($map)->delete();
		return;
	}
}