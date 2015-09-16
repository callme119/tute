<?php
/**
 *  任务
 *  panjie
 */
namespace Task\Model;
use Think\Model;
class TaskModel extends Model
{
	protected $_validate = array(
		array("cycle_id","require","cycle_id is required"),
		array("user_id","require","user_id is required"),
	);

	protected $_auto = array(
		array('time','time', 3 ,'function')//对添加或是编辑时，增加time的时间戳 
	);
}