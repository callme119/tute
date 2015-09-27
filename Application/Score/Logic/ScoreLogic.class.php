<?php
/**
 * 项目用户分值占比 逻辑
 */
namespace Score\Logic;
use Score\Model\ScoreModel;		//分值 占比表
class ScoreLogic extends ScoreModel
{
	public function getAllListsByProjectId($projectId)
	{
		$map['project_id'] = (int)$projectId;

		return $this->field($field)->where($map)->select();

	}

	/**
	 * 获取当前项目的总分值 
	 * @param  int $projectId  项目ID
	 * @return arrya            一维
	 */
	public function getSumPercentByProjectId($projectId)
	{
		$field["sum(score_percent)"] = "sum_percent";
		$map['project_id'] = $projectId;
		$data =  $this->field($field)->where($map)->find();
		// echo $this->getLastSql();
		return $data;
	}

	public function addByUserIdProjectIdScorePercent($userId, $projectId , $scorePercent = 100)
	{
		if( !$data['user_id'] = (int)$userId )
		{
			$this->error = "传入了空的userId";
			return false;
		}

		if( !$data['project_id'] = (int)$projectId )
		{
			$this->error = "传入了空的projectid";
			return false;
		}

		$data['score_percent'] = (int)$scorePercent;

		if($this->create($data))
		{
			$this->add();
			return true;
		}
		else
		{
			return false;
		}
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
        $map['s.user_id'] = $userId;
        $map['pc.type'] = $type;

        $field["s.user_id"] = "user_id";
        $field["s.project_id"] = "id";
        $field["p.title"] = "title";
        $field["p.project_category_id"] = "project_category_id";
        $field["p.time"] = "time";
        $field["p.cycle_id"] = "cycle_id";

        $field["pc.score"] = "score";
        
        $field["w.is_finished"] = "is_finished";
        

        $this->alias("s");      //设置表别名
        $this->totalCount = $this->join("left join __PROJECT__ p on s.project_id = p.id left join __PROJECT_CATEGORY__ pc on p.project_category_id = pc.id left join __WORKFLOW__ w on p.id=w.project_id")->where($map)->count();
        $this->alias("s");  
        $return = $this->where($map)->field($field)->join("left join __PROJECT__ p on s.project_id = p.id left join __PROJECT_CATEGORY__ pc on p.project_category_id = pc.id left join __WORKFLOW__ w on p.id=w.project_id")->page($this->p,$this->pageSize)->order($this->order)->select();   
        // echo $this->getLastSql();
        return $return;
    }

    /**
     * 返回某 项目ID 的用户占比信息
     * @param  int $projectId 项目ID
     * @return array            二组数据
     */
    public function getUsersPercentsByProjectId($projectId)
    {
    	$projectId = (int)$projectId;
    	$map['project_id'] = $projectId;
    	$datas = $this->where($map)->select();

    	$sumPercent = 0;
    	foreach($datas as $data)
    	{
    		$sumPercent += $data['score_percent'];
    	}

    	foreach($datas as $key => $data)
    	{
    		$datas[$key]['sum_percent'] = $sumPercent;
    	}
    	return $datas;
    }
}