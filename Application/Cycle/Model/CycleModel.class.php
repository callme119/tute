<?php
namespace Cycle\Model;
use Think\Model;
class CycleModel extends Model
{
	protected $_auto = array();
	protected $_validate = array(
		array('name','require','the name file is required')
		);
}