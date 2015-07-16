<?php

/*
 * 我的工作的Model类
 *
 * @autho denghapoyang
 * 275111108@qq.com
 */
namespace Myjob\Model;
use Think\Model;
class JobModel extends Model{
     private $map = null;
    /**
     * 设定map数组
     * @param array $map
     */
    public function setMap($map) {
        $this->map = $map;
    }
    /**
     * 根据传入的userid初始化待办工作数据
     * @param int $id
     * @return array
     */
    public function getJobIdByUserId() {   
        $relation = D('user_job');
        return $relation->where($this->map)->select();
    }
     /**
     * 根据map取项目数据
     * @param int $id
     * @return array
     */
    public function getResearchIdByJobId() {   
        return $this->where($this->map)->select();
    }
    
    
    public function index($id) {
       $map = array();
       $map['userid'] = $id;
       $this->setMap($map);
       $Jobid = $this->getJobIdByUserId();
       var_dump($Jobid);
       
    }  
}