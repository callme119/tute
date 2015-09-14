<?php
/**
 * 项目类别 logic
 */
namespace ProjectCategory\Logic;
use ProjectCategory\Model\ProjectCategoryModel;
class ProjectCategoryLogic extends ProjectCategoryModel
{
	/**
	 * 根据传入的子结点，返回所有的节点信息。
	 * @param  num $sonId 子节点信息
	 * @return array        二维数组
	 */
	public function getTreeBySonId($sonId)
	{
		//实例化
		$ProjectCategoryM = new ProjectCategoryModel();
		$return = array();
		$data = null;

		//先查找本结点，再查找上级结点。
		//当不存在结点信息时，退出。
		do{
			$return = $data;
			$map['id'] = $sonId;
			$data = $ProjectCategoryM->where($map)->find();
			$tem = $data;
			$sonId = $data['pid'];
			$data['_son'] = $return;
		}
		while($tem !== null );

		return $return;
	}

	/**
	 * 进行数组截断，显示本页数据
	 * @param  二维数据 $lists 
	 * @return 二维数组        [description]
	 */
	public function getCurrentLists($lists)
	{
		$p = is_numeric(I('get.p')) ? I('get.p') : "1";
	    $pageSize = C("PAGE_SIZE") ? C("PAGE_SIZE") : 20;
	    $totalCount = count($lists);
	    $offsize = ($p-1)*$pageSize;
	    
	    //查看是否不在当前页
	    if($totalCount < $p*$pageSize)
	    {
	      $p = ceil($totalCount/$pageSize);
	    }

	    //截取数据
	    $return = array_slice($lists , $offsize , $pageSize);
	    return $return;
	}

	/**
	 * 返回本结点及全部子结点信息
	 * @return tree 目录树
	 */
	public function getSonsTreeById($id , $keyWord = "_son")
	{
		$map = array();
		$map['pid'] = $id;
		$datas = $this->where($map)->select();
		foreach($datas as $key => $data)
		{
			$datas[$key][$keyWord] = $this->getSonsTreeById($data[id] , $keyWord);
		}
		return $datas;
	}

	/**
	 * 返回相关结点的子结点
	 * @return int 结点ID
	 */
	public function getListsByPid($pid)
	{
		$map['pid'] = $pid;
		try
		{
			$data = $this->where($map)->select();
			return $data;
		}
		catch(\Think\Exception $e)
		{
			$thie->error = "数据查询发生错误：错误信息：". $e->getMessage() . "最后查询语句: " . $this->getLastSql();
			return false;
		}
	}
	/**
	 * 查找相关ID的具体信息
	 * @param  int $id 项目类别ID
	 * @return array     一维
	 */
	public function getListById($id)
	{
		$map['id'] = $id;
		return $this->where($map)->find();
	}

	/**
	 * 删除
	 * @param  int $id 关键字
	 * @return [type]     [description]
	 */
	public function deleteById($id)
	{
		return $this->where("id = $id")->delete();
	}

	/**
	 * 返回相关 数据模型ID 的所有记录
	 * @param  [type] $dataModelId [description]
	 * @return [type]              [description]
	 */
	public function getListsByDataModelId($dataModelId)
	{
		$map['data_model_id'] = $dataModelId;
		return $this->where($map)->select();
	}
}