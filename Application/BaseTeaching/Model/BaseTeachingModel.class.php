<?php
namespace BaseTeaching\Model;
use Think\Model;
Class BaseTeachingModel extends Model
{
	protected $_auto = array(
		array("time","time",3,"function"),
	);

	protected $_validate = array(
		array("cycle_id","require","cycle_id is required"),
		array("user_id","require","user_id is required")
	);
}