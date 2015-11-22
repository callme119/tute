<?php
/**
 * 项目用户分值占比 逻辑
 */
namespace Score\Logic;
use Score\Model\ScoreModel;		       //分值 占比表
use Score\Model\ScoreViewModel;        //分值 占比表
class ScoreLogic extends ScoreModel
{
    protected $order = "time desc";
    
    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function deleteByProjectId($projectId)
    {
        $map['project_id'] = (int)$projectId;
        $data = $this->where($map)->delete();
        return $data;
    }

	public function getAllListsByProjectId($projectId)
	{
		$map['project_id'] = (int)$projectId;

		return $this->where($map)->select();

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
        $field["p.user_id"] = "commit_user_id";
        $field["p.time"] = "time";
        $field["p.cycle_id"] = "cycle_id";

        $field["pc.score"] = "score";
        
        $field["w.is_finished"] = "is_finished";

        $this->alias("s");      //设置表别名
        $this->totalCount = $this->join("left join __PROJECT__ p on s.project_id = p.id left join __PROJECT_CATEGORY__ pc on p.project_category_id = pc.id left join __WORKFLOW__ w on p.id=w.project_id")->where($map)->count();
        $this->alias("s");  
        $return = $this->where($map)->order($order)->field($field)->join("left join __PROJECT__ p on s.project_id = p.id left join __PROJECT_CATEGORY__ pc on p.project_category_id = pc.id left join __WORKFLOW__ w on p.id=w.project_id")->page($this->p,$this->pageSize)->order($this->order)->select();   
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

    /**
     * 通过 用户 周期 类型 获取相当记录集
     * @param  [type] $userId  [description]
     * @param  [type] $cycleId [description]
     * @param  [type] $type    [description]
     * @return [type]          [description]
     */
    public function getListsByUserIdCycleIdType($userId ,$cycleId , $type)
    {
        $ScoreV = new ScoreViewModel();

        $map['user_id'] = $userId;
        $map['cycle_id'] = $cycleId;
        $map['type'] = $type;

        $this->totalCount = $ScoreV->where($map)->count();
        $return = $ScoreV->where($map)->order($this->order)->select();
        // echo $ScoreV->getLastSql();
        return $return;
    }

    public function getListsByCycleIdType($cycleId , $type)
    {
        $ScoreV = new ScoreViewModel();
        $map['cycle_id'] = $cycleId;
        $map['type'] = $type;
        $this->totalCount = $ScoreV->where($map)->count();
        $return = $ScoreV->where($map)->select();
        return $return;
    }

    /**
     * 通过 用户ID 周期ID 项目类别type 获取完成的总分数
     * @param  int $userId  用户
     * @param  int $cycleid 周期
     * @param  string $type    项目类别的类型
     * @return int          
     */
    public function getScoresByUserIdCycleIdType($userId ,$cycleId ,$type)
    {
        $scores = $this->getListsByUserIdCycleIdType($userId ,$cycleId ,$type);
        $totalDoneScore = 0;
        $totalScore = 0;
        foreach($scores as $key => $score)
        {
            if($score['state'] == 1)
            {
                $totalDoneScore += (int)ceil($score['score_percent']*$score['project_category_score']/100);
            }
            $totalScore += (int)ceil($score['score_percent']*$score['project_category_score']/100);
        } 

        $return['total_score'] = $totalScore;
        $return['total_done_score'] = $totalDoneScore;

        return $return;
    }

    /**
     * 返户某用户 某个项目下的具体信息
     * @param  int $projectId  项目ID
     * @param  it $userId    用户ID
     * @return 一维数据            [description]
     */
    public function getListsByProjectIdUserId($projectId , $userId)
    {
        $map['project_id'] = (int)$projectId;
        $map['user_id'] = (int)$userId;
        return $this->where($map)->find();
    }

    public function getListByProjectIdUserId($projectId ,$userId)
    {
        return $this->getListsByProjectIdUserId($projectId , $userId);
    }
}