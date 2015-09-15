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
		array("user_id","requre","user_id is required"),
	);
}