<?php
/**
 * 分值分布关联视图模型
 */
namespace Score\Model;
use Think\Model\ViewModel;
class ScoreViewModel extends ViewModel
{
	public $viewFields = array(
		'score' => array('user_id','score_percent'),
		'project' => array('title'=>'title','id'=>'id', 'cycle_id'=>'cycle_id','_on'=>'score.project_id = project.id'),
		'project_category' => array('id'=>'project_category_id','score'=>'project_category_score','type'=>'type', '_on'=>'project.project_category_id=project_category.id'),
		'workflow'=>array('state'=>'state','_on'=>'project.id=workflow.project_id'),
	);
}